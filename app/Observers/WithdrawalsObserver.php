<?php

namespace App\Observers;

use App\Model\Withdrawal;
use App\Providers\EmailsServiceProvider;
use App\Providers\GenericHelperServiceProvider;
use App\Providers\NotificationServiceProvider;
use App\Providers\PaymentsServiceProvider;
use App\Providers\SettingsServiceProvider;
use App\Providers\UsersServiceProvider;
use App\Providers\WithdrawalsServiceProvider;
use App\User;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class WithdrawalsObserver
{
    /**
     * Listen to the Withdrawal updating event.
     *
     * @param Withdrawal $withdrawal
     * @return void
     * @throws ValidationException
     */
    public function saving(Withdrawal $withdrawal)
    {
        // we only care about admin handling here
        if(!UsersServiceProvider::loggedAsAdmin()) {
            return;
        }

        if($withdrawal->processed) {
            throw ValidationException::withMessages([
                __('This withdrawal request has already been processed'),
            ]);
        }

        if(!$withdrawal->exists && $withdrawal->status !== Withdrawal::REQUESTED_STATUS) {
            throw ValidationException::withMessages([
                __('A new withdrawal must be created with the requested status'),
            ]);
        }

        if($withdrawal->payment_method === Withdrawal::STRIPE_CONNECT_METHOD) {
            throw ValidationException::withMessages([
                __('Withdrawals using Stripe Connect can only be created by creators'),
            ]);
        }

        if ($withdrawal->status === Withdrawal::REQUESTED_STATUS) {
            self::handleWithdrawalCreation($withdrawal);
        }

        if($withdrawal->status === Withdrawal::REJECTED_STATUS) {
            self::handleWithdrawalRejection($withdrawal);
        }

        if($withdrawal->status === Withdrawal::APPROVED_STATUS) {
            self::handleWithdrawalApproval($withdrawal);
        }
    }

    /**
     * Handles the Withdrawal deletion event.
     *
     * @param Withdrawal $withdrawal
     * @return void
     */
    public function deleted(Withdrawal $withdrawal)
    {
        // we only care about admin handling here
        if(!UsersServiceProvider::loggedAsAdmin()) {
            return;
        }

        if(!$withdrawal->processed){
            self::handleWithdrawalRejection($withdrawal, true);
        }
    }

    /**
     * Returns money to the user and send notifications for a rejected/deleted withdrawal.
     * @param $withdrawal
     * @param bool $skipNotficationEntry
     */
    private function handleWithdrawalRejection($withdrawal, bool $skipNotficationEntry = false): void {
        WithdrawalsServiceProvider::creditUserForRejectedWithdrawal($withdrawal);
        $emailSubject = __('Your withdrawal request has been denied.');
        $button = [
            'text' => __('Try again'),
            'url' => route('my.settings', ['type'=>'wallet']),
        ];

        self::processWithdrawalNotifications($withdrawal, $emailSubject, $button, $skipNotficationEntry);
        // mark withdrawal as processed
        $withdrawal->processed = true;
    }

    private function handleWithdrawalApproval($withdrawal): void {
        PaymentsServiceProvider::createTransactionForWithdrawal($withdrawal);

        $emailSubject = __('Your withdrawal request has been approved.');
        $button = [
            'text' => __('My payments'),
            'url' => route('my.settings', ['type'=>'payments']),
        ];

        self::processWithdrawalNotifications($withdrawal, $emailSubject, $button);
        // mark withdrawal as processed
        $withdrawal->processed = true;
        // Adding fee if enabled
        if(getSetting('payments.withdrawal_allow_fees')){
            $withdrawal->fee = $withdrawal->amount * (getSetting('payments.withdrawal_default_fee_percentage') / 100);
        }
    }

    private function handleWithdrawalCreation($withdrawal) {
        $userWallet = $withdrawal->user->wallet;
        if(!$userWallet) {
            $userWallet = GenericHelperServiceProvider::createUserWallet($withdrawal->user);
        }

        $amount = $withdrawal->amount;
        if(floatval($amount) > $userWallet->total){
            throw ValidationException::withMessages([
                __("This user's credit balance is lower than the withdrawal amount. Try a lower amount"),
            ]);
        }

        $fee = 0;
        if(getSetting('payments.withdrawal_allow_fees') && floatval(getSetting('payments.withdrawal_default_fee_percentage')) > 0) {
            $fee = (floatval(getSetting('payments.withdrawal_default_fee_percentage')) / 100) * floatval($amount);
        }

        $withdrawal->fee = $fee;

        $userWallet->update([
            'total' => $userWallet->total - floatval($amount),
        ]);

        // Sending out admin email
        WithdrawalsServiceProvider::processNewWithdrawalEmailNotification();
    }

    /**
     * Creates email / user notifications.
     * @param $withdrawal
     * @param $emailSubject
     * @param $button
     * @param $skipNotficationEntry
     */
    private function processWithdrawalNotifications($withdrawal, $emailSubject, $button, $skipNotficationEntry = false) {
        // Sending out the user notification
        $user = User::find($withdrawal->user_id);
        try{
            App::setLocale($user->settings['locale']);
        }
        catch (\Exception $e){
            App::setLocale('en');
        }
        EmailsServiceProvider::sendGenericEmail(
            [
                'email' => $user->email,
                'subject' => $emailSubject,
                'title' => __('Hello, :name,', ['name'=>$user->name]),
                'content' => __('Email withdrawal processed', [
                        'siteName' => getSetting('site.name'),
                        'status' => __($withdrawal->status),
                    ]).($withdrawal->status == 'approved' ? ' '.SettingsServiceProvider::getWebsiteFormattedAmount($withdrawal->amount).(getSetting('payments.withdrawal_allow_fees') ? '(-'.SettingsServiceProvider::getWebsiteCurrencySymbol().($withdrawal->amount * (getSetting('payments.withdrawal_default_fee_percentage') / 100)).' taxes)' : '').' '.__('has been sent to your account.') : ''),
                'button' => $button,
            ]
        );

        // If withdrawal is deleted - do not create notification entry
        if(!$skipNotficationEntry){
            NotificationServiceProvider::createApprovedOrRejectedWithdrawalNotification($withdrawal);
        }
    }
}
