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
namespace Twitter\Controller;

require_once dirname(__DIR__) . "/Lib/TwitterOAuth/TwitterOAuth.php";
require_once dirname(__DIR__) . "/Lib/TwitterOAuth/Exception/TwitterException.php";

use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;
use Thelia\Model\ConfigQuery;
use Twitter\Twitter;
use Twitter\Form\ConfigurationForm;
use TwitterOAuth\TwitterOAuth;
use TwitterOAuth\Exception\TwitterException;


/**
 * Class ConfigController
 * @package Twitter\Controller
 * @author Nicolas Léon <nicolas@omnitic.com>
 */

class ConfigurationController extends BaseAdminController
{
    public function saveAction()
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, ['Twitter'], AccessManager::UPDATE))
        {
            return $response;
        }

        $form              = new ConfigurationForm($this->getRequest());
        $configurationForm = $this->validateForm($form);
        $consumer_key      = $configurationForm->get('consumer_key')->getData();
        $consumer_secret   = $configurationForm->get('consumer_secret')->getData();
        $screen_name       = $configurationForm->get('screen_name')->getData();
        $count             = $configurationForm->get('count')->getData();
        $cache_lifetime    = $configurationForm->get('cache_lifetime')->getData();
        // $debug_mode     = $configurationForm->get('debug_mode')->getData();
        $errorMessage      = null;
        $response          = null;

        // Save config values
        ConfigQuery::write('twitter_consumer_key', $consumer_key, 1, 1);
        ConfigQuery::write('twitter_consumer_secret', $consumer_secret, 1, 1);
        ConfigQuery::write('twitter_screen_name', $screen_name, 1, 1);
        ConfigQuery::write('twitter_count', $count, 1, 1);
        ConfigQuery::write('twitter_cache_lifetime', $cache_lifetime * 60, 1, 1); // Minutes
        ConfigQuery::write('twitter_last_updated', 0, 1, 1);

        if($screen_name && $consumer_key && $consumer_secret)
        {
            if(!extension_loaded('openssl'))
            {
                $sslError = $this->getTranslator()->trans("This module requires the PHP extension open_ssl to work.", [], Twitter::DOMAIN_NAME);
            }
            else
            {
                $config = array(
                    'consumer_key'       => $consumer_key,
                    'consumer_secret'    => $consumer_secret,
                    'output_format'      => 'array'
                );
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
                        'screen_name' => $screen_name,
                        'count' => 1, //$count,
                        'exclude_replies' => true
                    );
                    $response = $connection->get('statuses/user_timeline', $params);
                    if($response['error']) throw new TwitterException($response['error']);
                }
                catch(\Exception $e)
                {
                    $erroMessage = $this->getTranslator()->trans("Unrecognized screen name", [], Twitter::DOMAIN_NAME);
                }
            }
        }
        $response = RedirectResponse::create(URL::getInstance()->absoluteUrl('/admin/module/Twitter'));

        if (null !== $errorMessage) {
            $this->setupFormErrorContext(
                $this->getTranslator()->trans("Twitter configuration failed.", [], Twitter::DOMAIN_NAME),
                $errorMessage,
                $form
            );
            $response = $this->render(
                "module-configure",
                [
                    'module_code' => 'Twitter',
                ]
            );
        }
        return $response;
    }
}

