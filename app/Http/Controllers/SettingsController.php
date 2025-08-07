<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserTaxesRequest;
use App\Http\Requests\ProfileUploadRequest;
use App\Http\Requests\UpdateUserFlagSettingsRequest;
use App\Http\Requests\UpdateUserProfileSettingsRequest;
use App\Http\Requests\UpdateUserRatesSettingsRequest;
use App\Http\Requests\UpdateUserSettingsRequest;
use App\Http\Requests\VerifyProfileAssetsRequest;
use App\Model\Attachment;
use App\Model\Country;
use App\Model\CreatorOffer;
use App\Model\ReferralCodeUsage;
use App\Model\Subscription;
use App\Model\Transaction;
use App\Model\UserDevice;
use App\Model\UserGender;
use App\Model\UserTax;
use App\Model\UserVerify;
use App\Providers\AttachmentServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\EmailsServiceProvider;
use App\Providers\GenericHelperServiceProvider;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use JavaScript;
use Jenssegers\Agent\Agent;
use Ramsey\Uuid\Uuid;

class SettingsController extends Controller
{
    public $availableSettings = [
        'profile' => ['heading' => 'Update your bio, cover and avatar', 'icon' => 'person'],
        'account' => ['heading' => 'Manage your account settings', 'icon' => 'settings'],
        'wallet' => ['heading' => 'Your payments & wallet', 'icon' => 'wallet'],
        'payments' => ['heading' => 'Your payments & wallet', 'icon' => 'card'],
        'rates' => ['heading' => 'Prices & Bundles', 'icon' => 'layers'],
        'subscriptions' => ['heading' => 'Your active subscriptions', 'icon' => 'people'],
        'referrals' => ['heading' => 'Invite other people to earn more', 'icon' => 'person-add'],
        'notifications' => ['heading' => 'Your email notifications settings', 'icon' => 'notifications'],
        'privacy' => ['heading' => 'Your privacy and safety matters', 'icon' => 'shield'],
        'verify' => ['heading' => 'Get verified and start earning now', 'icon' => 'checkmark'],
        'tax-information' => ['heading' => 'Complete your tax details to receive payouts', 'icon' => 'cash'],
    ];

    public function __construct()
    {
        if(getSetting('site.hide_identity_checks')){
            unset($this->availableSettings['verify']);
        }

        if(!getSetting('payments.tax_info_dac7_enabled')) {
            unset($this->availableSettings['tax-information']);
        }
    }

    public function checkIfValidRoute($route)
    {
        if ($route) {
            if (!isset($this->availableSettings[$route])) {
                abort(404);
            }
        }
        return true;
    }

    public function index(Request $request)
    {
        $this->checkReferralAccess();
        $this->checkIfValidRoute($request->route('type'));
        $user = Auth::user();
        $userID = $user->id;
        $data = [];

        // ======================= FIX #1: READING THE SETTING =======================
        // Reading from the correct 'show_adult_content' column on the user model.
        $data['show_adult_content'] = $user->show_adult_content;
        // =========================================================================

        switch ($request->route('type')) {
            case 'wallet':
                JavaScript::put([
                    'stripeConfig' => [
                        'stripePublicID' => getSetting('payments.stripe_public_key'),
                    ],
                    'offlinePayments' => [
                        'offline_payments_make_notes_field_mandatory' => (bool)getSetting('payments.offline_payments_make_notes_field_mandatory'),
                        'offline_payments_minimum_attachments_required' => (int)getSetting('payments.offline_payments_minimum_attachments_required'),
                    ],
                ]);
                $activeWalletTab = $request->get('active');
                $data['activeTab'] = $activeWalletTab != null ? $activeWalletTab : 'deposit';
                if($activeWalletTab == 'withdraw') {
                    $data['userTax'] = UserTax::query()->where('user_id', $userID)->orderBy('id', 'desc')->first();
                }
                break;
            case 'subscriptions':
                $activeSubsTab = 'subscriptions';
                if($request->get('active')){
                    $activeSubsTab = $request->get('active');
                }
                if($activeSubsTab == 'subscriptions'){
                    $subscriptions = Subscription::with(['creator'])->where('sender_user_id', $userID)->orderBy('id', 'desc')->paginate(6);
                }
                else{
                    $subscriptions = Subscription::with(['creator'])->where('recipient_user_id', $userID)->orderBy('id', 'desc')->paginate(6);
                }
                $subscribersCount = Subscription::with(['creator'])->where('recipient_user_id', $userID)->orderBy('id', 'desc')->count();
                $data['subscriptions'] = $subscriptions;
                $data['subscribersCount'] = $subscribersCount;
                $data['activeSubsTab'] = $activeSubsTab;
                break;
            case 'subscribers':
                $subscribers = Subscription::with(['creator'])->where('recipient_user_id', $userID)->orderBy('id', 'desc')->paginate(2);
                $data['subscribers'] = $subscribers;
                break;
            case 'privacy':
                $devices = UserDevice::where('user_id', $userID)->orderBy('created_at', 'DESC')->get()->map(function ($item) {
                    $agent = new Agent();
                    $agent->setUserAgent($item->agent);
                    $deviceType = 'Desktop';
                    if($agent->isPhone()){
                        $deviceType = 'Mobile';
                    }
                    if($agent->isTablet()){
                        $deviceType = 'Tablet';
                    }
                    $item->setAttribute('device_type', $deviceType);
                    $item->setAttribute('browser', $agent->browser());
                    $item->setAttribute('device', $agent->device());
                    $item->setAttribute('platform', $agent->platform());
                    return $item;
                });
                $data['devices'] = $devices;
                $data['verifiedDevicesCount'] = UserDevice::where('user_id', $userID)->where('verified_at', '<>', null)->count();
                $data['unverifiedDevicesCount'] = UserDevice::where('user_id', $userID)->where('verified_at', null)->count();
                $data['countries'] = Country::all();
                JavaScript::put([
                    'userGeoBlocking' => [
                        'countries' => isset(Auth::user()->settings['geoblocked_countries']) ? json_decode(Auth::user()->settings['geoblocked_countries']) : [],
                        'enabled' => getSetting('security.allow_geo_blocking'),
                    ],
                ]);
                break;
            case 'payments':
                $payments = Transaction::with(['receiver', 'sender'])->where('sender_user_id', $userID)->orWhere('recipient_user_id', $userID)->orderBy('id', 'desc')->paginate(6);
                $data['payments'] = $payments;
                break;
            case null:
            case 'profile':
                JavaScript::put([
                    'bioConfig' => [
                        'allow_profile_bio_markdown' => getSetting('profiles.allow_profile_bio_markdown'),
                        'allow_profile_bio_markdown_links' => getSetting('profiles.allow_hyperlinks'),
                    ],
                ]);
                $data['genders'] = UserGender::all();
                $data['minBirthDate'] = Carbon::now()->subYear(18)->format('Y-m-d');
                $data['countries'] = Country::query()->where('name', '!=', 'All')->get();
                break;
            case 'referrals':
                if(getSetting('referrals.enabled')) {
                    if(empty($user->referral_code)){
                        $user->referral_code = AuthServiceProvider::generateReferralCode(8);
                        $user->save();
                    }
                    $data['referrals'] = ReferralCodeUsage::with(['usedBy'])->where('referral_code', $user->referral_code)->orderBy('id', 'desc')->paginate(6);
                }
                break;
            case 'rates':
                    $data['offer'] = $user->offer;
                break;
            case 'verify':
                $request->session()->forget('verifyAssets');
                break;
            case 'tax-information':
                $data['countries'] = Country::query()->where('name', '!=', 'All')->get();
                $data['userTax'] = UserTax::query()->where('user_id', $userID)->orderBy('id', 'desc')->first();
                break;
        }
        return $this->renderSettingView($request->route('type'), $data);
    }

    public function renderSettingView($route, $data = [])
    {
        $currentTab = $route ? $route : 'profile';
        $currentSettingTab = $this->availableSettings[$currentTab];
        JavaScript::put(
            [
                'mediaSettings' => [
                    'allowed_file_extensions' => '.'.str_replace(',', ',.', AttachmentServiceProvider::filterExtensions('imagesOnly')),
                    'max_file_upload_size' => (int) getSetting('media.max_file_upload_size'),
                    'manual_payments_file_extensions' => '.'.str_replace(',', ',.', AttachmentServiceProvider::filterExtensions('manualPayments')),
                    'manual_payments_excel_icon' => asset('/img/excel-preview.svg'),
                    'manual_payments_pdf_icon' => asset('/img/pdf-preview.svg'),
                    'initUploader' => (!Auth::user()->verification || (Auth::user()->verification && Auth::user()->verification->status !== 'verified' && Auth::user()->verification->status !== 'pending')),
                ],
            ]
        );
        return view('pages.settings', array_merge(
            $data,
            [
                'availableSettings' => $this->availableSettings,
                'currentSettingTab' => $currentSettingTab,
                'activeSettingsTab' => $currentTab,
                'additionalAssets'   => $this->getAdditionalRouteAssets($route),
            ]
        ));
    }

    public function saveProfile(UpdateUserProfileSettingsRequest $request)
    {
        $validator = $this->validateUsername($request->get('username'));
        if($validator->fails()){
            return back()->withErrors($validator);
        }
        $user = Auth::user();
        $user->update([
            'name' => $request->get('name'),
            'username' => $request->get('username'),
            'bio' => $request->get('bio'),
            'location' => $request->get('location'),
            'website' => $request->get('website'),
            'birthdate' => $request->get('birthdate'),
            'gender_id' => $request->get('gender'),
            'gender_pronoun' => $request->get('pronoun'),
            'country_id' => $request->get('country'),
        ]);
        return back()->with('success', __('Settings saved.'));
    }

    private function validateUsername($username) {
        $routes = [];
        foreach (Route::getRoutes() as $route)
        {
            $routes[] = $route->uri;
        }
        $validator = \Illuminate\Support\Facades\Validator::make(
            ['username' => $username],
            ['username' => 'not_in:'.implode(',', $routes)],
            ['username.*' => __('The selected username is invalid.')]
        );
        return $validator;
    }

    public function saveRates(Request $request)
    {
        $user = Auth::user();
        if ($request->get('is_offer')) {
            $offerExpireDate = $request->get('profile_access_offer_date');
            $currentOffer = CreatorOffer::where('user_id', Auth::user()->id)->first();
            $data = [
                'expires_at' => $offerExpireDate,
            ];
            if ($currentOffer) {
                if ($user->profile_access_price != $request->get('profile_access_price')) {
                    $data['old_profile_access_price'] = $user->profile_access_price;
                }
                if ($user->profile_access_price_6_months != $request->get('profile_access_price_6_months')) {
                    $data['old_profile_access_price_6_months'] = $user->profile_access_price_6_months;
                }
                if ($user->profile_access_price_12_months != $request->get('profile_access_price_12_months')) {
                    $data['old_profile_access_price_12_months'] = $user->profile_access_price_12_months;
                }
                if ($user->profile_access_price_3_months != $request->get('profile_access_price_3_months')) {
                    $data['old_profile_access_price_3_months'] = $user->profile_access_price_3_months;
                }
                $currentOffer->update($data);
            } else {
                $data = [
                    'expires_at' => $offerExpireDate,
                    'old_profile_access_price' => $user->profile_access_price,
                    'old_profile_access_price_6_months' => $user->profile_access_price_6_months,
                    'old_profile_access_price_12_months' => $user->profile_access_price_12_months,
                    'old_profile_access_price_3_months' => $user->profile_access_price_3_months,
                ];
                $data['user_id'] = $user->id;
                CreatorOffer::create($data);
            }
        } else {
            $currentOffer = CreatorOffer::where('user_id', Auth::user()->id)->first();
            if ($currentOffer) {
                $currentOffer->delete();
            }
        }
        $rules = UpdateUserRatesSettingsRequest::getRules();
        $trimmedRules = [];
        foreach($rules as $key => $rule){
            if(($request->get($key) != null) || $key == 'profile_access_price'){
                $trimmedRules[$key] = $rule;
            }
        }
        $request->validate($trimmedRules);
        $user->update([
            'profile_access_price' => $request->get('profile_access_price'),
            'profile_access_price_6_months' => $request->get('profile_access_price_6_months'),
            'profile_access_price_12_months' => $request->get('profile_access_price_12_months'),
            'profile_access_price_3_months' => $request->get('profile_access_price_3_months'),
        ]);
        return back()->with('success', __('Settings saved.'));
    }

    public function updateFlagSettings(UpdateUserFlagSettingsRequest $request)
    {
        $user = Auth::user();
        $key = $request->get('key');
        $value = filter_var($request->get('value'), FILTER_VALIDATE_BOOLEAN);

        // ======================= FIX #2: SAVING THE SETTING =======================
        // Added 'show_adult_content' to the list of allowed keys to be updated.
        if (!in_array($key, ['public_profile', 'paid-profile', 'enable_2fa', 'enable_geoblocking', 'open_profile', 'show_adult_content'])) {
            return response()->json(['success' => false, 'message' => __('Settings not saved')]);
        }
        // =========================================================================
        
        if($key === 'paid-profile'){
            $key = 'paid_profile';
        }
        if($key == 'enable_2fa'){
            if($value){
                $userDevices = UserDevice::where('user_id', $user->id)->get();
                if(count($userDevices) == 0){
                    AuthServiceProvider::addNewUserDevice($user->id, true);
                }
            }
        }
        $user->update([
            $key => $value,
        ]);
        return response()->json(['success' => true, 'message' => __('Settings saved')]);
    }

    public function saveAccount(UpdateUserSettingsRequest $request)
    {
        Auth::user()->update(['password'=>Hash::make($request->input('confirm_password'))]);
        return back()->with('success', __('Settings saved.'));
    }

    public function getAdditionalRouteAssets($settingRoute)
    {
        $additionalAssets = ['js' => [], 'css' => []];
        switch ($settingRoute) {
            case 'account':
                $additionalAssets['js'][] = '/js/pages/settings/account.js';
                break;
            case 'wallet':
                $additionalAssets['js'][] = '/js/pages/settings/deposit.js';
                $additionalAssets['js'][] = '/js/pages/settings/withdrawal.js';
                $additionalAssets['css'][] = '/libs/dropzone/dist/dropzone.css';
                $additionalAssets['js'][] = '/libs/dropzone/dist/dropzone.js';
                $additionalAssets['js'][] = '/js/FileUpload.js';
                break;
            case 'profile':
            case null:
                $additionalAssets['css'][] = '/libs/dropzone/dist/dropzone.css';
                $additionalAssets['js'][] = '/libs/dropzone/dist/dropzone.js';
                $additionalAssets['js'][] = '/js/pages/settings/profile.js';
                break;
            case 'privacy':
                $additionalAssets['js'][] = '/js/pages/settings/privacy.js';
                $additionalAssets['js'][] = '/js/pages/settings/notifications.js';
                $additionalAssets['js'][] = '/libs/@selectize/selectize/dist/js/standalone/selectize.min.js';
                $additionalAssets['css'][] = '/libs/@selectize/selectize/dist/css/selectize.css';
                $additionalAssets['css'][] = '/libs/@selectize/selectize/dist/css/selectize.bootstrap4.css';
                break;
            case 'notifications':
                $additionalAssets['js'][] = '/js/pages/settings/notifications.js';
                break;
            case 'subscriptions':
                $additionalAssets['js'][] = '/js/pages/settings/subscriptions.js';
                break;
            case 'verify':
                $additionalAssets['css'][] = '/libs/dropzone/dist/dropzone.css';
                $additionalAssets['js'][] = '/libs/dropzone/dist/dropzone.js';
                $additionalAssets['js'][] = '/js/pages/settings/verify.js';
                $additionalAssets['js'][] = '/js/FileUpload.js';
                break;
            case 'rates':
                $additionalAssets['js'][] = '/js/pages/settings/rates.js';
                break;
            case 'referrals':
                $additionalAssets['js'][] = '/js/pages/settings/referrals.js';
                $additionalAssets['css'][] = '/css/pages/referrals.css';
                break;
        }
        return $additionalAssets;
    }

    public function uploadProfileAsset(ProfileUploadRequest $request)
    {
        $file = $request->file('file');
        $type = $request->route('uploadType');
        try {
            $directory = 'users/'.$type;
            $s3 = Storage::disk(config('filesystems.defaultFilesystemDriver'));
            $fileId = Uuid::uuid4()->getHex();
            $filePath = $directory.'/'.$fileId.'.jpg';
            $img = Image::make($file);
            if ($type == 'cover') {
                $coverWidth = 599;
                $coverHeight = 180;
                if(getSetting('media.users_covers_size')){
                    $coverSizes = explode('x', getSetting('media.users_covers_size'));
                    if(isset($coverSizes[0])){
                        $coverWidth = (int)$coverSizes[0];
                    }
                    if(isset($coverSizes[1])){
                        $coverHeight = (int)$coverSizes[1];
                    }
                }
                $img->fit($coverWidth, $coverHeight)->orientate();
                $data = ['cover' => $filePath];
            } else {
                $avatarWidth = 96;
                $avatarHeight = 96;
                if(getSetting('media.users_avatars_size')){
                    $sizes = explode('x', getSetting('media.users_avatars_size'));
                    if(isset($sizes[0])){
                        $avatarWidth = (int)$sizes[0];
                    }
                    if(isset($sizes[1])){
                        $avatarHeight = (int)$sizes[1];
                    }
                }
                $img->fit($avatarWidth, $avatarHeight)->orientate();
                $data = ['avatar' => $filePath];
            }
            $img->encode('jpg', 100);
            Auth()->user()->update($data);
            $s3->put($filePath, $img, 'public');
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => ['file'=>$exception->getMessage()]]);
        }
        $assetPath = GenericHelperServiceProvider::getStorageAvatarPath($filePath);
        if($type == 'cover'){
            $assetPath = GenericHelperServiceProvider::getStorageCoverPath($filePath);
        }
        return response()->json(['success' => true, 'assetSrc' => $assetPath]);
    }

    public function removeProfileAsset(Request $request)
    {
        $type = $request->route('assetType');
        $data = ['avatar' => ''];
        if ($type == 'cover') {
            $data = ['cover' => ''];
        }
        Auth::user()->update($data);
        return response()->json(['success' => true, 'message' => ucfirst($type).' '.__("removed successfully").'.', 'data' => [
            'avatar' => Auth::user()->avatar,
            'cover' => Auth::user()->cover,
        ]]);
    }

    public function updateUserSettings(Request $request)
    {
        try {
            if (!in_array($request->key, [
                'notification_email_new_post_created',
                'notification_email_new_sub',
                'notification_email_new_message',
                'notification_email_expiring_subs',
                'notification_email_renewals',
                'notification_email_new_tip',
                'notification_email_new_comment',
                'geoblocked_countries',
                'notification_email_new_ppv_unlock',
                'notification_email_creator_went_live',
            ])) {
                return response()->json(['success' => false, 'message' => __('Invalid setting key')]);
            }

            User::where('id', Auth::user()->id)->update(['settings'=> array_merge(
                Auth::user()->settings ? Auth::user()->settings->toArray() : [],
                [$request->get('key') => $request->get('value')]
            )]);

            return response()->json(['success' => true, 'message' => __('Settings saved')]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => __('Settings not saved'), 'error' => $exception->getMessage()]);
        }
    }

    public function verifyUpload(VerifyProfileAssetsRequest $request)
    {
        $file = $request->file('file');
        try {
            $attachment = AttachmentServiceProvider::createAttachment($file, 'users/verifications');
            if ($request->session()->get('verifyAssets')) {
                $data = json_decode($request->session()->get('verifyAssets'));
                $data[] = $attachment->filename;
                session(['verifyAssets' => json_encode($data)]);
            } else {
                $data = [$attachment->filename];
                session(['verifyAssets' => json_encode($data)]);
            }
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'errors' => [$exception->getMessage()]], 500);
        }
        return response()->json([
            'success' => true,
            'attachmentID' => $attachment->id,
            'path' => Storage::url($attachment->filename),
            'type' => AttachmentServiceProvider::getAttachmentType($attachment->type),
            'thumbnail' => AttachmentServiceProvider::getThumbnailPathForAttachmentByResolution($attachment, 150, 150),
        ]);
    }

    public function deleteVerifyAsset(Request $request)
    {
        try {
            $assetSrc = $request->get('assetSrc');
            $data = $request->session()->get('verifyAssets');
            if (is_string($data)) {
                $data = json_decode($data, true);
            }
            if (!is_array($data)) {
                return response()->json([
                    'success' => false,
                    'errors' => ['file' => 'Session data is invalid.'],
                ]);
            }
            $foundPath = null;
            $newData = [];
            foreach ($data as $path) {
                if (strpos($path, $assetSrc) !== false) {
                    $foundPath = $path;
                    continue;
                }
                $newData[] = $path;
            }
            if ($foundPath === null) {
                return response()->json([
                    'success' => false,
                    'errors' => ['file' => 'Asset not found in session data.'],
                ]);
            }
            $request->session()->put('verifyAssets', json_encode($newData));
            $file = Attachment::where('user_id', Auth::id())
                ->where('id', $assetSrc)
                ->first();
            if ($file) {
                $file->delete();
            }
            return response()->json(['success' => true]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'errors' => ['file' => $exception->getMessage()],
            ]);
        }
    }

    public function saveVerifyRequest(Request $request)
    {
        if (getSetting('compliance.enforce_tos_check_on_id_verify')){
            if (!$request->get('terms')) {
                return back()->with('error', __('Please confirm the terms and conditions checkbox'));
            }
        }
        if (getSetting('compliance.enforce_media_agreement_on_id_verify')){
            if (!$request->get('media_checkbox')) {
                return back()->with('error', __('Please confirm the media agreement checkbox.'));
            }
        }
        if ($request->session()->get('verifyAssets')) {
            if (!Auth::user()->verification) {
                UserVerify::create([
                    'user_id' => Auth::user()->id,
                    'files' => $request->session()->get('verifyAssets'),
                ]);
            } else {
                Auth::user()->verification->update(
                    [
                        'user_id' => Auth::user()->id,
                        'files' => $request->session()->get('verifyAssets'),
                        'status' => 'pending',
                    ]
                );
            }
            $adminEmails = User::where('role_id', 1)->select(['email', 'name'])->get();
            foreach ($adminEmails as $user) {
                EmailsServiceProvider::sendGenericEmail(
                    [
                        'email' => $user->email,
                        'subject' => __('Action required | New identity check'),
                        'title' => __('Hello, :name,', ['name' => $user->name]),
                        'content' => __('There is a new identity check on :siteName that requires your attention.', ['siteName' => getSetting('site.name')]),
                        'button' => [
                            'text' => __('Go to admin'),
                            'url' => route('voyager.dashboard'),
                        ],
                    ]
                );
            }
            $request->session()->forget('verifyAssets');
            return back()->with('success', __('Request sent. You will be notified once your verification is processed.'));
        } else {
            return back()->with('error', __('Please attach photos with the front and back sides of your ID.'));
        }
    }

    public static function getCountries() {
        try {
            $countries = Country::all();
            return response()->json(['success' => true, 'data' => $countries]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => __('Could not fetch countries list.'), 'error' => $exception->getMessage()]);
        }
    }

    protected function checkReferralAccess() {
        $user = Auth::user();
        if(!getSetting('referrals.enabled')){
            unset($this->availableSettings['referrals']);
        }
        if(getSetting('referrals.disable_for_non_verified') && ($user->role_id !== 1)){
            if(!($user->email_verified_at && $user->birthdate && ($user->verification && $user->verification->status == 'verified'))){
                unset($this->availableSettings['referrals']);
            }
        }
    }

    public function addUserTaxInformation(AddUserTaxesRequest $request)
    {
        $user = Auth::user();
        $existingUserTax = UserTax::query()->where('user_id', $user->id)->orderBy('id', 'desc')->first();
        if($existingUserTax) {
            $existingUserTax->update([
                'legal_name' => $request->get('legalName'),
                'primary_address' => $request->get('primaryAddress'),
                'issuing_country_id' => $request->get('issuingCountry'),
                'tax_type' => $request->get('taxType'),
                'date_of_birth' => $request->get('dateOfBirth'),
                'tax_identification_number' => $request->get('taxIdentificationNumber'),
                'vat_number' => $request->get('vatNumber'),
            ]);
        } else {
            UserTax::create([
                'legal_name' => $request->get('legalName'),
                'primary_address' => $request->get('primaryAddress'),
                'issuing_country_id' => $request->get('issuingCountry'),
                'tax_type' => $request->get('taxType'),
                'date_of_birth' => $request->get('dateOfBirth'),
                'tax_identification_number' => $request->get('taxIdentificationNumber'),
                'vat_number' => $request->get('vatNumber'),
                'user_id' => $user->id,
            ]);
        }
        return back()->with('success', __('Tax information saved.'));
    }
}