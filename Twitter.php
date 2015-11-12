<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/


namespace Twitter;
// echo __DIR__;die();
// echo dirname(__DIR__);die();
require_once __DIR__ . "/Lib/TwitterOAuth/TwitterOAuth.php";
require_once __DIR__ . "/Lib/TwitterOAuth/Exception/TwitterException.php";

use Thelia\Module\BaseModule;
use Thelia\Core\Translation\Translator;
use Thelia\Model\ConfigQuery;
use TwitterOAuth\TwitterOAuth;
use TwitterOAuth\Exception\TwitterException;

class Twitter extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'twitter';
    const TWEETS_FILENAME = 'tweets.json';

    private $screen_name,
    		$consumer_key,
            $consumer_secret,
            $cache_lifetime,
    		$count;

    function __construct()
    {
            $this->screen_name     = ConfigQuery::read('twitter_screen_name');
            $this->consumer_key    = ConfigQuery::read('twitter_consumer_key');
            $this->consumer_secret = ConfigQuery::read('twitter_consumer_secret');
            $this->cache_lifetime  = ConfigQuery::read('twitter_cache_lifetime');
            $this->count           = ConfigQuery::read('twitter_count');
    }

    public function getTweets()
    {

        $cache_path = realpath('.') . '/cache/tweets';
        $last_updated = ConfigQuery::read('twitter_last_updated');
        $tweets_file = $cache_path . '/' . Twitter::TWEETS_FILENAME;
        $tweets = [];

        // Is the cache stale ?
        // if( $last_updated + 0 < time() )
        if( $last_updated + $this->cache_lifetime < time() )
        {
            if(!is_dir($cache_path))
            {
                mkdir($cache_path);
                chmod($cache_path, 0755);
            }
            // Get the tweets
            $config = [
                'consumer_key' => $this->consumer_key,
                'consumer_secret' => $this->consumer_secret
            ];

            try
            {
                $connection = new TwitterOAuth($config);
                $bearer_token = $connection->getBearerToken();
            }
            catch(\Exception $e)
            {
                $errorMessage =  $e->getMessage();
            }

            try
            {
                $params = array(
                    'screen_name' => $this->screen_name,
                    'count' => $this->count,
                    'exclude_replies' => true
                );
                $tweets = $connection->get('statuses/user_timeline', $params);
                if($tweets['error']) throw new TwitterException($tweets['error']);
                // Cache tweets
                unlink($tweets_file);
                $fh = fopen($tweets_file, 'w');
                fwrite($fh, json_encode($tweets));
                fclose($fh);
                // Update cache refresh timestamp
                ConfigQuery::write('twitter_last_updated', time(), 1, 1);
            }
            catch(\Exception $e)
            {
                $erroMessage = Translator::getInstance()->trans("Unrecognized screen name", [], Twitter::DOMAIN_NAME);
            }
        }
        else
        {
            // Get tweets from the cache
            $tweets = json_decode(file_get_contents($tweets_file));
        }
        return $tweets;
    }
}