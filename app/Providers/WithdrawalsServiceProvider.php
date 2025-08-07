<?php

namespace App\Providers;

use App\Model\Wallet;
use App\User;
use Illuminate\Support\ServiceProvider;

class WithdrawalsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public static function createStripeAccountForUser($user) {
        $stripeAccount = StripeServiceProvider::createStripeCustomAccount($user);
        $user->stripe_account_id = $stripeAccount->id;
        $user->save();
    }

    public static function userDoneStripeOnboarding($user): bool {
        $account = StripeServiceProvider::retrieveStripeCustomAccount($user->stripe_account_id);

        if($account->charges_enabled && $account->payouts_enabled) {
            return true;
        }

        return false;
    }

    /**
     * Restoring the money to the user.
     * @param $withdrawal
     */
    public static function creditUserForRejectedWithdrawal($withdrawal) {
        // Restoring the money to the user
        $userId = $withdrawal->user_id;
        $wallet = Wallet::where('user_id', $userId)->first();
        $wallet->update(['total' => $wallet->total + floatval($withdrawal->amount)]);
    }

    public static function processNewWithdrawalEmailNotification() {
        // Sending out admin email
        $adminEmails = User::where('role_id', 1)->select(['email', 'name'])->get();
        foreach ($adminEmails as $user) {
            EmailsServiceProvider::sendGenericEmail(
                [
                    'email' => $user->email,
                    'subject' => __('Action required | New withdrawal request'),
                    'title' => __('Hello, :name,', ['name' => $user->name]),
                    'content' => __('There is a new withdrawal request on :siteName that requires your attention.', ['siteName' => getSetting('site.name')]),
                    'button' => [
                        'text' => __('Go to admin'),
                        'url' => route('voyager.dashboard').'/withdrawals',
                    ],
                ]
            );
        }
    }
}
