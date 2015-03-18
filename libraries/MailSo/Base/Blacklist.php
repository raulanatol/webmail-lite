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
     * Verify if the emails is on blacklist or no.
     * @param \MailSo\Mime\Email $email
     * @return bool returns false if email on blacklist
     */
    private static function isValidEmail($email) {
        $emailToFind = strtolower($email->GetEmail());
        $emailDomain = 'TODO';
        //FIXME verify domain first
        if (Blacklist::emailOnBlackListTable($emailToFind)) {
            return false;
        }
        return true;
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
        $result = false;
        /** @var \CApiDbManager $oApiDbManager */
        $oApiDbManager = \CApi::Manager('db');
        if (!Blacklist::emailOnBlackListTable($emailToBlock)) {
            $result = $oApiDbManager->ExecuteQuery("INSERT INTO email_blacklist (email) VALUES ('" . strtolower($emailToBlock) . "')");
        } else {
            $result = true;
        }
        return $result;
    }

    /**
     * @param $email
     * @return bool
     */
    private static function emailOnBlackListTable($email) {
        $response = true;
        /** @var \CApiDbManager $oApiDbManager */
        $oApiDbManager = \CApi::Manager('db');
        $result = $oApiDbManager->GetSimpleQuery("SELECT count(1) as result FROM email_blacklist WHERE email = '" . strtolower($email) . "'");
        if ($result != null) {
            $response = (bool)$result->result;
        }
        return $response;
    }
}