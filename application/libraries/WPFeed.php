<?php

if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}

class WPFeed
{
  var $CI;

  public function __construct()
  {
    $this->CI = &get_instance();
  }

  public function posts($id = NULL)
  {
    return $this->fetch_response('posts', $id);
  }

  private function fetch_response($endpoint, $id = NULL, $args = NULL)
  {
    $endpoint = ENVIRONMENT == 'production' ? site_url('/blog/graphql') : 'http://veneto.ezoom.com.br/blog/graphql';

    $qry = '
query NewQuery {
  posts(last: 12) {
    edges {
      node {
        id
        date
        link
        title
        featuredImage {
          node {
            mediaDetails {
              sizes(include: MEDIUM_LARGE) {
                sourceUrl
              }
            }
            altText
          }
        }
        excerpt
      }
    }
  }
}';

    $headers = array();
    $headers[] = 'Content-Type: application/json';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $qry]));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if ($result) {
      return json_decode($result, true);
    }

    return FALSE;
  }
}
