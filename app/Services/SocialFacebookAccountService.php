<?php
namespace App\Services;
use App\SocialFacebookAccount;
use App\CustomerAuth;
use Laravel\Socialite\Contracts\User as ProviderUser;
class SocialFacebookAccountService
{
    public function createOrGetUser(ProviderUser $providerUser)
    {
        $account = SocialFacebookAccount::whereProvider('facebook')
            ->whereProviderUserId($providerUser->getId())
            ->first();
        if ($account) {
            return $account->user;
        } else {
            $account = new SocialFacebookAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => 'facebook'
            ]);
            $user = CustomerAuth::whereEmail($providerUser->getEmail())->first();
            if (!$user) {
                $user = CustomerAuth::create([
                    'email' => $providerUser->getEmail(),
                    'usernmae' => $providerUser->getName(),
                    'password' => bcrypt(rand(1,10000)),
                ]);
            }
            $account->user()->associate($user);
            $account->save();
            return $user;
        }
    }
}