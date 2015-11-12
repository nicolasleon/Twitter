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

namespace Twitter\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\ConfigQuery;
use Twitter\Twitter;

/**
 * Class Backend
 * @package Twitter\Form
 * @author Nicolas Léon <nicolas@omnitic.com>
 */
class ConfigurationForm extends BaseForm
{

    protected function buildForm()
    {
        $translator = Translator::getInstance();

        $this->formBuilder
        ->add(
            'consumer_key',
            'text',
            [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => $translator->trans('Consumer key', [], 'Twitter'),
                'label_attr' => array
                (
                  "for" => "consumer_key"
                ),
          'data' => ConfigQuery::read('twitter_consumer_key')
            ]
        )
        ->add(
            'consumer_secret',
            'text',
            [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => $translator->trans('Consumer secret', [], 'Twitter'),
                'label_attr' => array
                (
                    "for" => "consumer_secret"
                ),
                'data' => ConfigQuery::read('twitter_consumer_secret')
            ]
        )
        ->add(
            'screen_name',
            'text',
            [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => $translator->trans('Screen name', [], 'Twitter'),
                'label_attr' => array
                (
                    "for" => "screen_name",
                ),
                'data' => ConfigQuery::read('twitter_screen_name')
            ]
        )
        ->add(
            'count',
            'text',
            [
                'label' => $translator->trans('Number of tweets to cache', [], 'Twitter'),
                'label_attr' => array
                (
                    "for" => "count",
                    'help' => $translator->trans('Number of tweets to cache', [], 'Twitter'),
                ),
                'data' => ConfigQuery::read('twitter_count')
            ]
        )
        ->add(
            'cache_lifetime',
            'text',
            [
                'label' => $translator->trans('Cache lifetime', [], 'Twitter'),
                'label_attr' => array
                (
                    "for" => "cache_lifetime",
                    'help' => $translator->trans('Cache lifetime', [], 'Twitter'),
                ),
                'data' => ConfigQuery::read('twitter_cache_lifetime') / 60
            ]
        )

        ;
    }

    /**
     * @return string The name of you form.
     */
    public function getName()
    {
        return 'twitter_setup';
    }
}

