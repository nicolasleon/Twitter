# Twitter module for Thelia

This module displays tweets on you store.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Twitter.
* Activate it in your thelia administration panel

## Usage

This modules requires a Consumer Key and Consumer Key. To get these information you must create a Twitter App here https://apps.twitter.com/
Once created, copy the Consumer Key and Consumer secret from the Keys ans access tokens tab in the module configuration screen


## Loop

The module provide the folowing loop :

[tweets]

### Input arguments

|Argument |Description |
|---      |--- |
|**count** | The number of tweets to display. |

### Output arguments

|Variable   |Description |
|---        |--- |
|$TEXT    | The tweet text |
|$CREATED_AT    | The date of creation of the tweet (Unix timestamp)|

### Exemple
	<ul class="my-tweets">
	{loop name="my_tweets_loop" type="tweets" count="5"}
		<li>{$TEXT nofilter} - {$CREATED_AT|date_format:'d/m/Y'}</li>
	{/loop}
	</ul>

## Todo
<ul>
<li>Account profile loop</li>
<li>display_link argument description</li>
<li>Readme french translation</li>

