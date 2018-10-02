<?php

namespace Etable;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class ApiClient extends Client
{
    const BASE_URI = 'https://api.e-table.gr';
    const NAME     = 'yrizos/etable-api-client';
    const VERSION  = '0.3';
    const LANGUAGE = 'en';
    const TIMEOUT  = 30;

    private $timeout = self::TIMEOUT;

    public function __construct(array $config = [])
    {
        $token    = isset($config['token']) ? $config['token'] : '';
        $language = isset($config['language']) ? $config['language'] : self::LANGUAGE;
        $timeout  = isset($config['timeout']) && is_numeric($config['timeout']) ? intval($config['timeout']) : self::TIMEOUT;

        unset($config['token'], $config['language'], $config['timeout']);

        $this->setTimeout($timeout);

        if (!isset($config['base_uri'])) {
            $config['base_uri'] = self::BASE_URI;
        }

        $headers                    = isset($config['headers']) && is_array($config['headers']) ? $config['headers'] : [];
        $headers['User-Agent']      = self::getUserAgent();
        $headers['Authorization']   = 'Bearer ' . $token;
        $headers['Accept-Language'] = $language;
        $headers['Accept']          = 'application/json';

        $config['headers'] = $headers;

        parent::__construct($config);
    }

    public function setTimeout(int $timeout): ApiClient
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function request($method, $uri = '', array $options = [])
    {
        if (!isset($options['timeout'])) {
            $options['timeout'] = $this->getTimeout();
        }

        return parent::request($method, $uri, $options);
    }

    public function getNotifications(int $user_id): array
    {
        $response = $this->request('GET', '/v4/notifications/' . $user_id);

        return self::getArrayResponse($response);
    }

    public function getNotification(int $notification_id): array
    {
        $response = $this->request('GET', '/v4/notification/' . $notification_id);

        return self::getArrayResponse($response);
    }

    public function createUserSignedUpMessage(int $user_id)
    {
        return $this->createNotification([
            'user_id' => $user_id,
            'type'    => 'user_sign_up',
        ]);
    }

    public function createUserReviewUpvotedMessage(int $user_id, int $review_id, int $reviewer_id)
    {
        return $this->createNotification([
            'user_id'     => $user_id,
            'type'        => 'user_review_upvoted',
            'review_id'   => $review_id,
            'reviewer_id' => $reviewer_id,
        ]);
    }

    public function createUserReviewReminderMessage(int $user_id, int $review_id)
    {
        return $this->createNotification([
            'user_id'   => $user_id,
            'type'      => 'user_review_reminder',
            'review_id' => $review_id,
        ]);
    }

    public function createUserReviewFolloweeMessage(int $review_id)
    {
        $response = $this->request('POST', '/v4/review-notifications/' . $review_id, []);

        return self::getArrayResponse($response);
    }

    public function createUserNpsReminderMessage(int $user_id)
    {
        return $this->createNotification([
            'user_id' => $user_id,
            'type'    => 'user_nps_reminder',
        ]);
    }

    public function createUserLoyaltyPointsSummaryMessage(int $user_id, int $points)
    {
        return $this->createNotification([
            'user_id' => $user_id,
            'type'    => 'user_loyalty_points_summary',
            'points'  => $points,
        ]);
    }

    public function createUserGotNewFollowerMessage(int $user_id, int $follower_id)
    {
        return $this->createNotification([
            'user_id'     => $user_id,
            'type'        => 'user_follower_new',
            'follower_id' => $follower_id,
        ]);
    }

    public function createUser1000LoyaltyPointsMessage(int $user_id)
    {
        return $this->createNotification([
            'user_id' => $user_id,
            'type'    => 'user_loyalty_points_1000',
        ]);
    }

    public function createNotification(array $params = [])
    {
        $options  = ['form_params' => $params];
        $response = $this->request('POST', '/v4/notification', $options);

        return self::getArrayResponse($response);
    }

    public static function getUserAgent()
    {
        return 'yrizos/etable-api-client/' . self::VERSION . ' (+https://gitlab.com/yrizos/etable-api-client)';
    }

    public static function getArrayResponse(ResponseInterface $response)
    {
        $response = json_decode($response->getBody(), true);
        $response = isset($response['data']) ? $response['data'] : [];

        return $response;
    }
}
