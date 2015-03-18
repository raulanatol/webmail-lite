<?php

namespace MailSo\Base;


class Blacklist {

    /**
     * @param string $rawEmailList
     * @return bool
     */
    public static function blacklist($rawEmailList) {
        $validEmails = array();
        $emailList = Blacklist::rawToEmailCollection($rawEmailList);
        if ($rawEmailList) {
            /** @var \MailSo\Mime\Email $emailToVerify */
            foreach ($emailList as $emailToVerify) {
                if (Blacklist::isValidEmail($emailToVerify)) {
                    $validEmails[] = $emailToVerify->ToString();
                }
            }
        }
        return implode(',', $validEmails);
    }

    /**
     * @param \MailSo\Mime\Email $email
     * @return bool
     */
    private static function isValidEmail($email) {
        if ($email->GetEmail() == 'backlist@gmail.com') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $rawEmailList
     * @return array
     */
    private static function rawToEmailCollection($rawEmailList) {
        $oToEmails = \MailSo\Mime\EmailCollection::NewInstance($rawEmailList);
        return $oToEmails->GetAsArray();
    }

    /**
     * @param $emailToBlock
     * @return bool
     */
    public static function addEmailToBlackList($emailToBlock) {
        return true;
    }
}