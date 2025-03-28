<?php
namespace Controller;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class firebase {
    private $factory;
	
	public function __construct() {
		$this->factory = (new Factory)->withServiceAccount('shorten-url-4bcf9-cb32f4f56a14.json');
	}

	public function verifyUserToken($firebaseIdToken) {
		$auth = $this->factory->createAuth();
		try {
			$verifiedIdToken = $auth->verifyIdToken($firebaseIdToken);
		} catch (\Throwable $e) {
			throw $e;
			return null;
		}
		return $verifiedIdToken->claims()->get('sub');;
	}

	public function getUserRecord($firebaseUID) {
		$auth = $this->factory->createAuth();
		try {
			$userInfo = $auth->getUser($firebaseUID);
		} catch (\Throwable $e) {
			throw $e;
			return null;
		}
		return $userInfo;
	}

    public function sendDeviceMessage($devToken, $title, $content, $data = []) {
		$messaging = $this->factory->createMessaging();		
		$message = CloudMessage::withTarget('token', $devToken)
			->withNotification(Notification::create($title, $content)) // optional
			->withData($data) // optional
		;

		$ret = $messaging->send($message);
		//print_r($ret);
		return $ret;
	}

    public function sendTopicMessage($topic, $title, $content, $data = []) {
		$messaging = $this->factory->createMessaging();		
		$message = CloudMessage::withTarget('topic', $topic)
			->withNotification(Notification::create($title, $content)) // optional
			->withData($data) // optional
		;

		$ret = $messaging->send($message);
		//print_r($ret);
		return $ret;
	}

    public function createDynamicLink($url) {
	$dynamicLinks = $this->factory->createDynamicLinksService("https://bizwave.page.link");
	$link = $dynamicLinks->createShortLink($url);
	return $link;
    }

    public function getLinkStatistics($url, $duration = 7) {
	$dynamicLinks = $this->factory->createDynamicLinksService("https://bizwave.page.link");
	$stat = $dynamicLinks->getStatistics($url, $duration);
	return $stat;
    }
}