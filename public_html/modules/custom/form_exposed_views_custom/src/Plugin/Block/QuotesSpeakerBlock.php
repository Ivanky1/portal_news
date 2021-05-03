<?php

namespace Drupal\form_exposed_views_custom\Plugin\Block;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\form_exposed_views_custom\HelperMaterialsBase;

/**
 * Provides a 'Quotes Speaker' Block.
 *
 * @Block(
 *   id = "quotes_speaker_block",
 *   admin_label = @Translation("Сказано ранее блок цитат по совпадению спикера"),
 *   category = @Translation("quotes speaker block"),
 * )
 */
class QuotesSpeakerBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        $build = [];
        $build['#cache']['max-age'] = 0;
        $current_uri = \Drupal::request()->getRequestUri();
        $url_parser = UrlHelper::parse($current_uri);
        $url_query = $url_parser['query'];
        $date_filter = '';

        if (isset($url_query['publication']) && $url_query['publication'] != '') {
            $date_filter = substr($url_query['publication'], 6).
                '-'.substr($url_query['publication'], 3, 2).
                '-'.substr($url_query['publication'], 0, 2);
        }

        $result_quote = HelperMaterialsBase::getNewsWithData('quote', $date_filter, 10);
        $quotes_speaker = HelperMaterialsBase::getDataOfType(
            $result_quote, 'quote', 'speaker'
        );

        $speakers_news = HelperMaterialsBase::getSpeakersNews();


        return $build;
    }

}


