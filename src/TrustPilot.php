<?php

namespace TrustPilot {

    /**
     * Class TrustPilot
     * @author  Edwin Hoksberg <info@edwinhoksberg.nl>
     * @version 1.0
     * This class will provide useful functions to interact with the TrustPilot API data.
     */
    class Api
    {
        /**
         * To find your api key, login the trustpilot and navigate to modules.
         * Click on Trustpilot Integrations and find this link:
         * http://s.trustpilot.com/tpelements/1234567/f.json.gz
         * In this example, 1234567 is your api key.
         *
         * @var string The API key for trustpilot
         */
        private $api_key;

        /**
         * @var string The base api url, used for fetching the data
         */
        private $base_url = 'http://s.trustpilot.com/tpelements/%s/f.json.gz';

        /**
         * @var object This variable will hold all review data
         */
        private $jsonData;

        /**
         * Initialize the TrustPilot API.
         *
         * @param string $api_key The API provided by Trustpilot
         */
        public function __construct($api_key)
        {
            $this->api_key = $api_key;

            $this->loadAndParseData();
        }

        /**
         * Returns the total rating score for this site,
         * ranging from 0 to 100.
         *
         * @return int The rating score
         */
        public function getRatingScore()
        {
            return $this->jsonData->TrustScore->Score;
        }

        /**
         * Returns the star score for this site,
         * ranging from 0 to 5.
         *
         * @return int The stars score
         */
        public function getRatingStars()
        {
            return $this->jsonData->TrustScore->Stars;
        }

        /**
         * Returns a string containing the rating score.
         * Example:
         *  - Good
         *  - Excellent
         *  - etc...
         *
         * @return string The rating string
         */
        public function getRatingString()
        {
            return $this->jsonData->TrustScore->Human;
        }

        /**
         * This function returns an image of the site rating,
         * in the size you specified.
         *
         * @param string $imageSize The image size
         *
         * @return string The rating Image
         */
        public function getRatingImage($imageSize = null)
        {
            switch ($imageSize) {
                case 'small':
                    return $this->jsonData->TrustScore->StarsImageUrls->small;
                case 'medium':
                    return $this->jsonData->TrustScore->StarsImageUrls->medium;
                case 'large':
                    return $this->jsonData->TrustScore->StarsImageUrls->large;
                default:
                    return false;
            }
        }

        /**
         * Gets the number of reviews filled in for this site.
         *
         * @return int Review count
         */
        public function getReviewCount()
        {
            return $this->jsonData->ReviewCount->Total;
        }

        /**
         * Returns an array showing rating distrubution.
         *
         * @return array Rating stars ordered by rating
         */
        public function getReviewStarDistrubution()
        {
            return [
                1 => $this->jsonData->ReviewCount->DistributionOverStars[0],
                2 => $this->jsonData->ReviewCount->DistributionOverStars[1],
                3 => $this->jsonData->ReviewCount->DistributionOverStars[2],
                4 => $this->jsonData->ReviewCount->DistributionOverStars[3],
                5 => $this->jsonData->ReviewCount->DistributionOverStars[4],
            ];
        }

        /**
         * Returns the url for the review page.
         *
         * @return string Page url
         */
        public function getReviewPageUrl()
        {
            return $this->jsonData->ReviewPageUrl;
        }

        /**
         * Contains an array with all the reviews filled in for this site.
         *
         * @return array|bool All reviews or false if nothing was found
         */
        public function getAllReviews($minimumRating = 0, $locale = 'any')
        {
            $reviews = [];
            foreach ($this->jsonData->Reviews as $review) {
                if ($review->TrustScore->Score >= $minimumRating && $review->User->Locale == $locale || $locale == 'any') {
                    $reviews[] = $this->parseReview($review);
                }
            }

            return (!empty($reviews)) ? $reviews : false;
        }

        /**
         * Returns the latest review for this site.
         *
         * @param int $minimumRating Minimum review rating
         * @param string $locale The language code
         * @return array|bool The review, or if none found returns false
         */
        public function getFirstReview($minimumRating = 0, $locale = 'any')
        {
            foreach ($this->jsonData->Reviews as $review) {
                if ($review->TrustScore->Score >= $minimumRating && $review->User->Locale == $locale || $locale == 'any') {
                    return $this->parseReview($review);
                }
            }

            return false;
        }

        /**
         * Returns an random review, you can use the minimumRating parameter
         * to only return reviews of a minimum site score.
         * The locale parameter is to select a review from a specific country.
         *
         * @param int $minimumRating Minimum site rating
         * @param string $locale The language code
         *
         * @return array The review
         */
        public function getRandomReview($minimumRating = 0, $locale = 'any')
        {
            shuffle($this->jsonData->Reviews);

            foreach ($this->jsonData->Reviews as $review) {
                if ($review->TrustScore->Score >= $minimumRating && $review->User->Locale == $locale || $locale == 'any') {
                    return $this->parseReview($review);
                }
            }

            return false;
        }

        /**
         * This function returns an useable and formatted review
         *
         * @param object $review The plain review
         *
         * @return array The formatted review
         */
        private function parseReview($review)
        {
            return [
                'title'       => $review->Title,
                'content'     => $review->Content,
                'name'        => $review->User->Name,
                'url'         => $review->Url,
                'language'    => $review->User->Locale,
                'is_verified' => $review->User->IsVerified,
                'score'       => $review->TrustScore->Score,
                'stars'       => $review->TrustScore->Stars,
                'score_value' => $review->TrustScore->Human,
                'timestamp'   => $review->Created->UnixTime,
                'rating_images' => [
                    'small'  => $review->TrustScore->StarsImageUrls->small,
                    'medium' => $review->TrustScore->StarsImageUrls->medium,
                    'large'  => $review->TrustScore->StarsImageUrls->large,
                ]
            ];
        }

        /**
         * This function will load all review data, decompress it, and decode it for api use.
         */
        private function loadAndParseData()
        {
            $url = sprintf($this->base_url, $this->api_key);

            if (function_exists('gzdecode')) {
                $stringData = gzdecode(file_get_contents($url));
            } else {
                $stringData = gzuncompress(file_get_contents($url));
            }

            $this->jsonData = json_decode($stringData);
        }
    }
}
