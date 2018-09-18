<?php

namespace Etable;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class ApiClient extends Client
{
    const DEFAULT_BASE_URI = 'https://api.e-table.gr';
    const NAME             = 'yrizos/etable-api-client';
    const VERSION          = '0.1';
    const DEFAULT_LANGUAGE = 'en';

    public function __construct(array $config = [])
    {
        $token    = isset($config['token']) ? $config['token'] : '';
        $language = isset($config['language']) ? $config['language'] : self::DEFAULT_LANGUAGE;

        unset($config['token'], $config['language']);

        if (!isset($config['base_uri'])) {
            $config['base_uri'] = self::DEFAULT_BASE_URI;
        }

        $headers                    = isset($config['headers']) && is_array($config['headers']) ? $config['headers'] : [];
        $headers['User-Agent']      = self::getUserAgent();
        $headers['Authorization']   = 'Bearer ' . $token;
        $headers['Accept-Language'] = $language;

        $config['headers'] = $headers;

        parent::__construct($config);
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

    final public function createUserSignedUpMessage(int $user_id)
    {
        return $this->createNotification([
            'user_id' => $user_id,
            'type'    => 'user_sign_up',
        ]);
    }

    final public function createUserReviewUpvotedMessage(int $user_id, int $review_id, int $reviewer_id)
    {
        return $this->createNotification([
            'user_id'     => $user_id,
            'type'        => 'user_review_upvoted',
            'review_id'   => $review_id,
            'reviewer_id' => $reviewer_id,
        ]);
    }

    final public function createUserReviewReminderMessage(int $user_id, int $review_id)
    {
        return $this->createNotification([
            'user_id'   => $user_id,
            'type'      => 'user_review_reminder',
            'review_id' => $review_id,
        ]);
    }

    final public function createUserReviewFolloweeMessage(int $user_id, int $review_id)
    {
        return $this->createNotification([
            'user_id'   => $user_id,
            'type'      => 'user_review_followee',
            'review_id' => $review_id,
        ]);
    }

    final public function createUserNpsReminderMessage(int $user_id)
    {
        return $this->createNotification([
            'user_id' => $user_id,
            'type'    => 'user_nps_reminder',
        ]);
    }

    final public function createUserLoyaltyPointsSummaryMessage(int $user_id, int $points)
    {
        return $this->createNotification([
            'user_id' => $user_id,
            'type'    => 'user_loyalty_points_summary',
            'points'  => $points,
        ]);
    }

    final public function createUserGotNewFollowerMessage(int $user_id, int $follower_id)
    {
        return $this->createNotification([
            'user_id'     => $user_id,
            'type'        => 'user_follower_new',
            'follower_id' => $follower_id,
        ]);
    }

    final public function createUser1000LoyaltyPointsMessage(int $user_id)
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

}
