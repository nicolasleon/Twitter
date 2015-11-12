<?php

/**
*
*
*
*/
namespace Twitter\Loop;

use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Argument\Argument;
use Twitter\Twitter;

class TwitterLoop extends BaseLoop implements ArraySearchLoopInterface {

    public $countable = true;
    public $timestampable = false;
    public $versionable = false;

    public function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('start', 0),
            Argument::createIntTypeArgument('count', 5, true),
            Argument::createBooleanTypeArgument('display_link', true, false)
        );
    }

    public function buildArray()
    {
        $twitter = new Twitter();
        $tweets = $twitter->getTweets();
        $start = (int) $this->getStart();
        $count = (int) $this->getCount();

        return array_slice($tweets, $start, $count);

    }

    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $item) {
            // var_dump($item); die();
            $loopResultRow = new LoopResultRow();

            if( (bool)$this->getDisplayLink() == true )
            {
                $tweet = $item->text;

                // Screen name link
                $pattern = '@(https?://([-\w\.]+)+(/([\w/_\.]*(\?\S+)?(#\S+)?)?)?)@';
                $replacement = '<a href="$1">$1</a>';
                $tweet = preg_replace($pattern, $replacement, $tweet);

                // HTTP(S) link
                $pattern = '/@(\w+)/i';
                $replacement = '<a href="https://www.twitter.com/$1">@$1</a>';
                $tweet = preg_replace($pattern, $replacement, $tweet);

                // Hashtag link
                $pattern = '/\s+#(\w+)/';
                $replacement = ' <a href="http://search.twitter.com/search?q=%23$1">#$1</a>';
                $tweet = preg_replace($pattern, $replacement, $tweet);

                $loopResultRow->set("TEXT", preg_replace($pattern, $replacement, $tweet));
            }
            else
            {
                $loopResultRow->set("TEXT", $item->text);
            }
            $datetime = new \DateTime($item->created_at);
            $datetime->setTimezone(new \DateTimeZone('Europe/Zurich'));
            // echo $datetime->format('U');
            // echo $item->created_at;
            // die(strtotime($item->created_at));
            $loopResultRow->set("CREATED_AT", $datetime->format('U'));

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}