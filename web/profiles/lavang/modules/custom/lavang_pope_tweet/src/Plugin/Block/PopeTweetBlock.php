<?php

namespace Drupal\lavang_pope_tweet\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Component\Utility\SafeMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

use TwitterAPIExchange;

/**
 * Provides a 'PopeTweetBlock' block.
 *
 * @Block(
 *   id = "lavang_pope_tweet_block",
 *   admin_label = @Translation("Pope Francis"),
 *   category = @Translation("Lavang Block")
 * )
 */
class PopeTweetBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new PopeTweetBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    /** Set access tokens here - see: https://dev.twitter.com/apps/ **/
    $settings = array(
        'oauth_access_token' => "",
        'oauth_access_token_secret' => "",
        'consumer_key' => "nm1OhyAI2SPSAsJHz7auE4RbA",
        'consumer_secret' => "rT42tcLs9kNHy8tqAQJCdJB2QfflVRkmZaZSyzM7eQXcYMQBds"
    );

    // Set here the Twitter account from where getting latest tweets
    $screen_name = 'Pontifex';

    /** Perform a GET request and echo the response **/
    /** Note: Set the GET field BEFORE calling buildOauth(); **/
    $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    $getfield = '?screen_name=Pontifex&count=1&tweet_mode=extended';
    $requestMethod = 'GET';
    $twitter = new TwitterAPIExchange($settings);

    $recent_tweet = $twitter
                      ->setGetfield($getfield)
                      ->buildOauth($url, $requestMethod)
                      ->performRequest();

    $recent_tweet = json_decode($recent_tweet, TRUE);
// print_r($recent_tweet);
    $time_difference = REQUEST_TIME - strtotime($recent_tweet[0]["created_at"]);
    $time_ago = \Drupal::service('date.formatter')->formatInterval($time_difference, 1, 'vi');
    $retweet_count = number_format_short($recent_tweet[0]["retweet_count"]);
    $favorite_count = number_format_short($recent_tweet[0]["favorite_count"]);

    $markup = '';

    $markup .= '<div class="lavang-pope-tweet-time-ago">';
    $markup .= '<span class="lavang-pope-tweet-time">' . $time_ago . ' ago</span>';
    $markup .= '<img src="/profiles/lavang/themes/lavang_theme/images/twitter.svg" alt="twitter">';
    $markup .= '</div>';
    $markup .= '<p class="lavang-pope-tweet-text">' . SafeMarkup::checkPlain($recent_tweet[0]["full_text"]) .'</p>';

    $markup .= '<div class="lavang-pope-tweet-footer">';
    $markup .= '<span class="lavang-pope-tweet-favorite-count">' . $favorite_count . ' likes</span>';
    $markup .= '<span class="lavang-pope-tweet-retweet-count">' . $retweet_count . ' spread the word</span>';
    $markup .= '</div>';

    $markup .= '<a class="usa-button" href="https://twitter.com/Pontifex" target="_blank">Read more Pope\'s tweet</a>';

    // $markup .=  '<p>test '. date('H-i-s') .'</p>';

    $build['content'] = [
      '#markup' => $markup,
    ];

    $build['#cache']['max-age'] = 0;

    return $build;
  }

}


/**
 * @param $n
 * @return string
 * Use to convert large positive numbers in to short form like 1K+, 100K+, 199K+, 1M+, 10M+, 1B+ etc
 */
 function number_format_short( $n, $precision = 1 ) {
 	if ($n < 900) {
 		// 0 - 900
 		$n_format = number_format($n, $precision);
 		$suffix = '';
 	} else if ($n < 900000) {
 		// 0.9k-850k
 		$n_format = number_format($n / 1000, $precision);
 		$suffix = 'K';
 	} else if ($n < 900000000) {
 		// 0.9m-850m
 		$n_format = number_format($n / 1000000, $precision);
 		$suffix = 'M';
 	}
   // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
   // Intentionally does not affect partials, eg "1.50" -> "1.50"
 	if ( $precision > 0 ) {
 		$dotzero = '.' . str_repeat( '0', $precision );
 		$n_format = str_replace( $dotzero, '', $n_format );
 	}
 	return $n_format . $suffix;
 }
