<?php

namespace Etable;

use GuzzleHttp\Exception\ClientException;

class InternalApiClient extends ApiClient
{

    public function getNotifications (int $user_id): array
    {
        $response = $this->request('GET', '/internal/notifications/' . $user_id);

        return self::getArrayResponse($response);
    }

    public function getNotification (int $notification_id): array
    {
        $response = $this->request('GET', '/internal/notification/' . $notification_id);

        return self::getArrayResponse($response);
    }

    public function deleteNotification (int $notification_id): bool
    {
        try {
            $response = $this->request('DELETE', '/internal/notification/' . $notification_id);
        } catch (ClientException $e) {

            return FALSE;
        }

        return $response->getStatusCode() === 200;
    }

    public function setNotificationReadStatus (int $notification_id): bool
    {
        try {
            $response = $this->request('POST', '/internal/notification/' . $notification_id . '/read');
        } catch (ClientException $e) {

            return FALSE;
        }

        return $response->getStatusCode() === 200;
    }

    public function createUserSignedUpMessage (int $user_id)
    {
        return $this->createNotification([
            'user_id' => $user_id,
            'type'    => 'user_sign_up',
        ]);
    }

    public function createUserReviewUpvotedMessage (int $review_id, int $reviewer_id)
    {
        $options  = ['form_params' => ['reviewer_id' => $reviewer_id]];
        $response = $this->request('POST', '/internal/notification/review-upvoted/' . $review_id, $options);

        return self::getArrayResponse($response);
    }

    public function createUserReviewReminderMessage (int $user_id, int $review_id)
    {
        return $this->createNotification([
            'user_id'   => $user_id,
            'type'      => 'user_review_reminder',
            'review_id' => $review_id,
        ]);
    }

    public function createUserReviewPublishedMessage (int $review_id)
    {
        $response = $this->request('POST', '/internal/notification/review-published/' . $review_id, []);

        return self::getArrayResponse($response);
    }

    public function createUserNpsReminderMessage (int $user_id)
    {
        return $this->createNotification([
            'user_id' => $user_id,
            'type'    => 'user_nps_reminder',
        ]);
    }

    public function createUserLoyaltyPointsSummaryMessage (int $user_id, int $points)
    {
        return $this->createNotification([
            'user_id' => $user_id,
            'type'    => 'user_loyalty_points_summary',
            'points'  => $points,
        ]);
    }

    public function createUserGotNewFollowerMessage (int $user_id, int $follower_id)
    {
        return $this->createNotification([
            'user_id'     => $user_id,
            'type'        => 'user_follower_new',
            'follower_id' => $follower_id,
        ]);
    }

    public function createUser1000LoyaltyPointsMessage (int $user_id)
    {
        return $this->createNotification([
            'user_id' => $user_id,
            'type'    => 'user_loyalty_points_1000',
        ]);
    }

    public function createNotification (array $params = [])
    {
        $options  = ['form_params' => $params];
        $response = $this->request('POST', '/internal/notification', $options);

        return self::getArrayResponse($response);
    }
}