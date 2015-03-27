<?php

namespace MailSo\Base;

include_once 'mailchimp.php';

class Blacklist {
    const MAILCHIMP_API_KEY = '123e06bb665c261f04de2e816161cf4d-us10';

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
        return Blacklist::isValidEmailString($email->GetEmail());
    }

    /**
     * Verify if the emails is on blacklist or no.
     * @param string $email
     * @return bool returns false if email on blacklist
     */
    public static function isValidEmailString($email) {
        $emailToFind = Blacklist::safeValue($email);
        $emailDomain = Blacklist::getDomainFromEmail($emailToFind);

        if (Blacklist::domainOnBlackListTable($emailDomain)) {
            return false;
        }

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
        /** @var \CApiDbManager $oApiDbManager */
        $oApiDbManager = \CApi::Manager('db');
        if (!Blacklist::emailOnBlackListTable($emailToBlock)) {
            $result = $oApiDbManager->ExecuteQuery("INSERT INTO email_blacklist (email) VALUES ('" . Blacklist::safeValue($emailToBlock) . "')");
            Blacklist::syncUnSubscribeWithMailchimp($emailToBlock);
        } else {
            $result = true;
        }
        return $result;
    }

    public static function addDomainToBlacklist($domainToBlock) {
        /** @var \CApiDbManager $oApiDbManager */
        $oApiDbManager = \CApi::Manager('db');
        if (!Blacklist::domainOnBlackListTable($domainToBlock)) {
            $result = $oApiDbManager->ExecuteQuery("INSERT INTO domain_blacklist (domain) VALUES ('" . Blacklist::safeValue($domainToBlock) . "')");
        } else {
            $result = true;
        }
        return $result;
    }

    public static function removeEmailFromBlacklist($emailToReopen) {
        /** @var \CApiDbManager $oApiDbManager */
        $oApiDbManager = \CApi::Manager('db');
        $result = $oApiDbManager->ExecuteQuery("DELETE FROM email_blacklist WHERE email = '" . $emailToReopen . "'");
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
        $result = $oApiDbManager->GetSimpleQuery("SELECT count(1) as result FROM email_blacklist WHERE email = '" . Blacklist::safeValue($email) . "'");
        if ($result != null) {
            $response = (bool)$result->result;
        }
        return $response;
    }

    private static function domainOnBlackListTable($domainToBlock) {
        $response = true;
        /** @var \CApiDbManager $oApiDbManager */
        $oApiDbManager = \CApi::Manager('db');
        $result = $oApiDbManager->GetSimpleQuery("SELECT count(1) as result FROM domain_blacklist WHERE domain = '" . Blacklist::safeValue($domainToBlock) . "'");
        if ($result != null) {
            $response = (bool)$result->result;
        }
        return $response;
    }


    private static function safeValue($value) {
        return strtolower(trim($value));
    }

    private static function getDomainFromEmail($email) {
        $domain = substr(strrchr($email, '@'), 1);
        return $domain;
    }

    private static function syncUnSubscribeWithMailchimp($emailToBlock) {
        $mailchimp = new \MailChimp(Blacklist::MAILCHIMP_API_KEY);
        $listIds = Blacklist::getAllListOfMailchimp($mailchimp);
        foreach ($listIds as $listId) {
            $dataToSend = array(
                'apikey' => Blacklist::MAILCHIMP_API_KEY,
                'id' => $listId,
                'email' => array('email' => $emailToBlock),
                'delete_member' => true,
                'send_goodbye' => false,
                'send_notify' => false
            );
            $mailchimp->call('lists/unsubscribe', $dataToSend);
        }
    }

    /**
     * @param \MailChimp $mailchimp
     * @return array
     */
    private static function getAllListOfMailchimp($mailchimp) {
        $listsResult = $mailchimp->call('lists/list', array(
            'apikey' => Blacklist::MAILCHIMP_API_KEY
        ));
        $result = array();
        if ($listsResult['total'] > 0) {
            foreach ($listsResult['data'] as $listElement) {
                $result[] = $listElement['id'];
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    public static function getNewMassiveEmails() {
        /** @var \CApiDbManager $oApiDbManager */
        $oApiDbManager = \CApi::Manager('db');
        $queryResult = $oApiDbManager->GetSelect("SELECT * FROM massive_email_list WHERE ENABLED = 1 LIMIT 50");
        $resultList = array();
        $removeIds = array();
        foreach ($queryResult as $email) {
            $resultList[] = $email->email;
            $removeIds[] = $email->id;
        }
        $resultData = implode(',', $resultList);
        $oApiDbManager->ExecuteQuery("UPDATE massive_email_list SET ENABLED=0 WHERE id in (" . implode(',', $removeIds) . ")");
        return $resultData;
    }
}