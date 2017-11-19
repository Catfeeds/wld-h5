<?php

class PushPayload {
    private static $EFFECTIVE_DEVICE_TYPES = array('ios', 'android', 'winphone');
    private static $LIMIT_KEYS = array('X-Rate-Limit-Limit'=>'rateLimitLimit', 'X-Rate-Limit-Remaining'=>'rateLimitRemaining', 'X-Rate-Limit-Reset'=>'rateLimitReset');
    const PUSH_URL = 'https://api.jpush.cn/v3/push';
    const PUSH_VALIDATE_URL = 'https://api.jpush.cn/v3/push/validate';
    private $client;
    private $platform;

    private $audience;
    private $tags;
    private $tagAnds;
    private $alias;
    private $registrationIds;

    private $notificationAlert;
    private $iosNotification;
    private $androidNotification;
    private $winPhoneNotification;
    private $smsMessage;
    private $message;
    private $options;

    //以下是自己添加
    private $post = 'POST';
    private $disable_sound = '_disable_Sound' ;
    private $disable_badge = 0x10000;
    /**
     * PushPayload constructor.
     * @param $client JPush
     */
    function __construct($client) {
        $this->client = $client;
    }

    public function setPlatform($platform) {
        if (is_string($platform) && strcasecmp("all", $platform) === 0) {
            $this->platform = "all";
        } else {
            if (!is_array($platform)) {
                $platform = func_get_args();
                if (count($platform) <= 0) {
                    $custom_message = "Missing argument for PushPayload::setPlatform()";
                    return $custom_message;
                }
            }

            $_platform = array();
            foreach($platform as $type) {
                $type = strtolower($type);
                if (!in_array($type, self::$EFFECTIVE_DEVICE_TYPES)) {
                    $custom_message = "Invalid device type: " . $type;
                    return $custom_message;
                }
                if (!in_array($type, $_platform)) {
                    array_push($_platform, $type);
                }
            }
            $this->platform = $_platform;
        }
        return $this;
    }

    public function setAudience($all) {
        if (strtolower($all) === 'all') {
            $this->addAllAudience();
        } else {
            $custom_message = 'Invalid audience value';
            return $custom_message;
        }
    }

    public function addAllAudience() {
        $this->audience = "all";
        return $this;
    }

    public function addTag($tag) {
        if (is_null($this->tags)) {
            $this->tags = array();
        }

        if (is_array($tag)) {
            foreach($tag as $_tag) {
                if (!is_string($_tag)) {
                    $custom_message = "Invalid tag value";
                    return $custom_message;
                }
                if (!in_array($_tag, $this->tags)) {
                    array_push($this->tags, $_tag);
                }
            }
        } else if (is_string($tag)) {
            if (!in_array($tag, $this->tags)) {
                array_push($this->tags, $tag);
            }
        } else {
            $custom_message = "Invalid tag value";
            return $custom_message;
        }

        return $this;

    }

    public function addTagAnd($tag) {
        if (is_null($this->tagAnds)) {
            $this->tagAnds = array();
        }

        if (is_array($tag)) {
            foreach($tag as $_tag) {
                if (!is_string($_tag)) {
                    $custom_message = "Invalid tag_and value";
                    return $custom_message;
                }
                if (!in_array($_tag, $this->tagAnds)) {
                    array_push($this->tagAnds, $_tag);
                }
            }
        } else if (is_string($tag)) {
            if (!in_array($tag, $this->tagAnds)) {
                array_push($this->tagAnds, $tag);
            }
        } else {
            $custom_message = "Invalid tag_and value";
            return $custom_message;
        }

        return $this;
    }

    public function addAlias($alias) {
        if (is_null($this->alias)) {
            $this->alias = array();
        }

        if (is_array($alias)) {
            foreach($alias as $_alias) {
                if (!is_string($_alias)) {
                    $custom_message = "Invalid alias value";
                    return $custom_message;
                }
                if (!in_array($_alias, $this->alias)) {
                    array_push($this->alias, $_alias);
                }
            }
        } else if (is_string($alias)) {
            if (!in_array($alias, $this->alias)) {
                array_push($this->alias, $alias);
            }
        } else {
            $custom_message = "Invalid alias value";
            return $custom_message;
        }

        return $this;
    }

    public function addRegistrationId($registrationId) {
        if (is_null($this->registrationIds)) {
            $this->registrationIds = array();
        }

        if (is_array($registrationId)) {
            foreach($registrationId as $_registrationId) {
                if (!is_string($_registrationId)) {
                    $custom_message = "Invalid registration_id value";
                    return $custom_message;
                }
                if (!in_array($_registrationId, $this->registrationIds)) {
                    array_push($this->registrationIds, $_registrationId);
                }
            }
        } else if (is_string($registrationId)) {
            if (!in_array($registrationId, $this->registrationIds)) {
                array_push($this->registrationIds, $registrationId);
            }
        } else {
            $custom_message = "Invalid registration_id value";
            return $custom_message;
        }

        return $this;
    }

    public function setNotificationAlert($alert) {
        if (!is_string($alert)) {
            $custom_message = "Invalid alert value";
            return $custom_message;
        }
        $this->notificationAlert = $alert;
        return $this;
    }

    public function addIosNotification($alert=null, $sound=null, $badge=null, $content_available=null, $category=null, $extras=null) {
        $ios = array();

        if (!is_null($alert)) {
            if (!is_string($alert) && !is_array($alert)) {
                $custom_message = "Invalid ios alert value";
                return $custom_message;
            }
            $ios['alert'] = $alert;
        }

        if (!is_null($sound)) {
            if (!is_string($sound)) {
                $custom_message = "Invalid ios sound value";
                return $custom_message;
            }
            if ($sound !== $disable_sound) {
                $ios['sound'] = $sound;
            }
        } else {
            // 默认sound为''
            $ios['sound'] = '';
        }

        if (!is_null($badge)) {
            if (is_string($badge) && !preg_match("/^[+-]{1}[0-9]{1,3}$/", $badge)) {
                if (!is_int($badge)) {
                    $custom_message = "Invalid ios badge value";
                    return $custom_message;
                }
            }
            if ($badge != $disable_badge) {
                $ios['badge'] = $badge;
            }
        } else {
            // 默认badge为'+1'
            $ios['badge'] = '+1';
        }

        if (!is_null($content_available)) {
            if (!is_bool($content_available)) {
                $custom_message = "Invalid ios content-available value";
                return $custom_message;
            }
           // $ios['content-available'] = $content_available;
            $ios['mutable-content'] = $content_available;
        }

        if (!is_null($category)) {
            if (!is_string($category)) {
                $custom_message = "Invalid ios category value";
                return $custom_message;
            }
            if (strlen($category)) {
                $ios['category'] = $category;
            }
        }

        if (!is_null($extras)) {
            if (!is_array($extras)) {
                $custom_message = "Invalid ios extras value";
                return $custom_message;
            }
            if (count($extras) > 0) {
                $ios['extras'] = $extras;
            }
        }

        if (count($ios) <= 0) {
            $custom_message = "Invalid iOS notification";
            return $custom_message;
        }

        $this->iosNotification = $ios;
        return $this;
    }

    public function addAndroidNotification($alert=null, $title=null, $builderId=null, $extras=null) {
        $android = array();

        if (!is_null($alert)) {
            if (!is_string($alert)) {
                $custom_message = "Invalid android alert value";
                return $custom_message;
            }
            $android['alert'] = $alert;
        }

        if (!is_null($title)) {
            if(!is_string($title)) {
                $custom_message = "Invalid android title value";
                return $custom_message;
            }
            if(strlen($title) > 0) {
                $android['title'] = $title;
            }
        }

        if (!is_null($builderId)) {
            if (!is_int($builderId)) {
                $custom_message = "Invalid android builder_id value";
                return $custom_message;
            }
            $android['builder_id'] = $builderId;
        }

        if (!is_null($extras)) {
            if (!is_array($extras)) {
                $custom_message = "Invalid android extras value";
                return $custom_message;
            }
            if (count($extras) > 0) {
                $android['extras'] = $extras;
            }
        }

        if (count($android) <= 0) {
            $custom_message = "Invalid android notification";
            return $custom_message;
        }

        $this->androidNotification = $android;
        return $this;
    }

    public function addWinPhoneNotification($alert=null, $title=null, $_open_page=null, $extras=null) {
        $winPhone = array();

        if (!is_null($alert)) {
            if (!is_string($alert)) {
                $custom_message = "Invalid winphone notification";
                return $custom_message;
            }
            $winPhone['alert'] = $alert;
        }

        if (!is_null($title)) {
            if (!is_string($title)) {
                $custom_message = "Invalid winphone notification";
                return $custom_message;
            }
            if(strlen($title) > 0) {
                $winPhone['title'] = $title;
            }
        }

        if (!is_null($_open_page)) {
            if (!is_string($_open_page)) {
                $custom_message = "Invalid winphone _open_page notification";
                return $custom_message;
            }
            if (strlen($_open_page) > 0) {
                $winPhone['_open_page'] = $_open_page;
            }
        }

        if (!is_null($extras)) {
            if (!is_array($extras)) {
                $custom_message = "Invalid winphone extras notification";
                return $custom_message;
            }
            if (count($extras) > 0) {
                $winPhone['extras'] = $extras;
            }
        }

        if (count($winPhone) <= 0) {
            $custom_message = "Invalid winphone notification";
            return $custom_message;
        }

        $this->winPhoneNotification = $winPhone;
        return $this;
    }

    public function setSmsMessage($content, $delay_time) {
        $sms = array();
        if (is_null($content) || !is_string($content) || strlen($content) < 0 || strlen($content) > 480) {
            $custom_message = 'Invalid sms content, sms content\'s length must in [0, 480]';
            return $custom_message;
        } else {
            $sms['content'] = $content;
        }

        if (is_null($delay_time) || !is_int($delay_time) || $delay_time < 0 || $delay_time > 86400) {
            $custom_message = 'Invalid sms delay time, delay time must in [0, 86400]';
            return $custom_message;
        } else {
            $sms['delay_time'] = $delay_time;
        }

        $this->smsMessage = $sms;
        return $this;
    }


    public function setMessage($msg_content, $title=null, $content_type=null, $extras=null) {
        $message = array();

        if (is_null($msg_content) || !is_string($msg_content)) {
            $custom_message = "Invalid message content";
            return $custom_message;
        } else {
            $message['msg_content'] = $msg_content;
        }

        if (!is_null($title)) {
            if (!is_string($title)) {
                $custom_message = "Invalid message title";
                return $custom_message;
            }
            $message['title'] = $title;
        }

        if (!is_null($content_type)) {
            if (!is_string($content_type)) {
                $custom_message = "Invalid message content type";
                return $custom_message;
            }
            $message["content_type"] = $content_type;
        }

        if (!is_null($extras)) {
            if (!is_array($extras)) {
                $custom_message = "Invalid message extras";
                return $custom_message;
            }
            if (count($extras) > 0) {
                $message['extras'] = $extras;
            }
        }

        $this->message = $message;
        return $this;
    }

    public function setOptions($sendno=null, $time_to_live=null, $override_msg_id=null, $apns_production=null, $big_push_duration=null) {
        $options = array();

        if (!is_null($sendno)) {
            if (!is_int($sendno)) {
                $custom_message = "Invalid option sendno";
                return $custom_message;
            }
            $options['sendno'] = $sendno;
        } else {
            $options['sendno'] = $this->generateSendno();
        }

        if (!is_null($time_to_live)) {
            if (!is_int($time_to_live) || $time_to_live < 0 || $time_to_live > 864000) {
                $custom_message = "Invalid option time to live, it must be a int and in [0, 864000]";
                return $custom_message;
            }
            $options['time_to_live'] = $time_to_live;
        }

        if (!is_null($override_msg_id)) {
            if (!is_long($override_msg_id)) {
                $custom_message = "Invalid option override msg id";
                return $custom_message;
            }
            $options['override_msg_id'] = $override_msg_id;
        }

        if (!is_null($apns_production)) {
            if (!is_bool($apns_production)) {
                $custom_message = "Invalid option apns production";
                return $custom_message;
            }
            $options['apns_production'] = $apns_production;
        } else {
            $options['apns_production'] = false;
        }

        if (!is_null($big_push_duration)) {
            if (!is_int($big_push_duration) || $big_push_duration < 0 || $big_push_duration > 1440) {
                $custom_message = "Invalid option big push duration, it must be a int and in [0, 1440]";
                return $custom_message;
            }
            $options['big_push_duration'] = $big_push_duration;
        }

        $this->options = $options;
        return $this;
    }

    public function build() {
        $payload = array();

        // validate platform
        if (is_null($this->platform)) {
            $custom_message = "platform must be set";
            return $custom_message;
        }
        $payload["platform"] = $this->platform;

        // validate audience
        $audience = array();
        if (!is_null($this->tags)) {
            $audience["tag"] = $this->tags;
        }
        if (!is_null($this->tagAnds)) {
            $audience["tag_and"] = $this->tagAnds;
        }
        if (!is_null($this->alias)) {
            $audience["alias"] = $this->alias;
        }
        if (!is_null($this->registrationIds)) {
            $audience["registration_id"] = $this->registrationIds;
        }

        if (is_null($this->audience) && count($audience) <= 0) {
            $custom_message = "audience must be set";
            return $custom_message;
        } else if (!is_null($this->audience) && count($audience) > 0) {
            $custom_message = "you can't add tags/alias/registration_id/tag_and when audience='all'";
            return $custom_message;
        } else if (is_null($this->audience)) {
            $payload["audience"] = $audience;
        } else {
            $payload["audience"] = $this->audience;
        }


        // validate notification
        $notification = array();

        if (!is_null($this->notificationAlert)) {
            $notification['alert'] = $this->notificationAlert;
        }

        if (!is_null($this->androidNotification)) {
            $notification['android'] = $this->androidNotification;
            if (is_null($this->androidNotification['alert'])) {
                if (is_null($this->notificationAlert)) {
                    $custom_message = "Android alert can not be null";
                    return $custom_message;
                } else {
                    $notification['android']['alert'] = $this->notificationAlert;
                }
            }
        }

        if (!is_null($this->iosNotification)) {
            $notification['ios'] = $this->iosNotification;
            if (is_null($this->iosNotification['alert'])) {
                if (is_null($this->notificationAlert)) {
                    $custom_message = "iOS alert can not be null";
                    return $custom_message;
                } else {
                    $notification['ios']['alert'] = $this->notificationAlert;
                }
            }
        }

        if (!is_null($this->winPhoneNotification)) {
            $notification['winphone'] = $this->winPhoneNotification;
            if (is_null($this->winPhoneNotification['alert'])) {
                if (is_null($this->winPhoneNotification)) {
                    $custom_message = "WinPhone alert can not be null";
                    return $custom_message;
                } else {
                    $notification['winphone']['alert'] = $this->notificationAlert;
                }
            }
        }

        if (count($notification) > 0) {
            $payload['notification'] = $notification;
        }

        if (count($this->message) > 0) {
            $payload['message'] = $this->message;
        }
        if (!array_key_exists('notification', $payload) && !array_key_exists('message', $payload)) {
            $custom_message = "notification and message can not all be null";
            return $custom_message;
        }

        if (count($this->smsMessage)) {
            $payload['sms_message'] = $this->smsMessage;
        }

        if (count($this->options) > 0) {
            $payload['options'] = $this->options;
        } else {
            $this->setOptions();
            $payload['options'] = $this->options;
        }
        return $payload;
    }

    public function toJSON() {
        $payload = $this->build();
        return json_encode($payload);
    }

    public function printJSON() {
        echo $this->toJSON();
        return $this;
    }

    public function send() {
        $response = $this->client->_request(PushPayload::PUSH_URL,$post, $this->toJSON());
        return $this->__processResp($response);
    }

    public function validate() {
        $response = $this->client->_request(PushPayload::PUSH_VALIDATE_URL, $post , $this->toJSON());
        return $this->__processResp($response);
    }

    private function __processResp($response) {
        if($response['http_code'] === 200) {
            $body = array();
            $body['data'] = json_decode($response['body']);
            $headers = $response['headers'];
            if (is_array($headers)) {
                $limit = array();
                foreach (self::$LIMIT_KEYS as $key => $value) {
                    if (array_key_exists($key, $headers)) {
                        $limit[$value] = $headers[$key];
                    }
                }
                if (count($limit) > 0) {
                    $body['limit'] = (object)$limit;
                }
                return (object)$body;
            }
            return $body;
        } else {
            return Message(2001,$response['body']);
        }
    }
    private function generateSendno() {
        return rand(100000, 4294967294);
    }

}