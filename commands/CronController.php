<?php

namespace app\commands;

use app\models\History;
use app\models\search\HistorySearch;
use DateTime;
use Yii;
use yii\console\Controller;
use Elasticsearch\ClientBuilder;

$vendor = Yii::getAlias("@vendor");
require "${vendor}/autoload.php";

class CronController extends Controller
{
    public function actionCheckPeaksMemory()
    {

        $json = '{
  "size": 0, 
  "query": {
    "bool": {
      "must": [
        {
          "exists": {
            "field": "system.memory.actual.used.pct"
          }
        }
      ],
      "filter": [
        {
          "range": {
            "@timestamp": {
              "gte": "now-1m/m",
              "lte": "now"
            }
          }
        }
      ]
    }
  },
  "aggs": {
    "interval": {
      "date_histogram": {
        "field": "@timestamp",
        "fixed_interval": "1m",
        "order": {
          "_key": "desc"
        }
      },
      "aggs": {
        "host": {
          "terms": {
            "field": "host.name.keyword"
          },
          "aggs": {
            "value": {
              "avg": {
                "field": "system.memory.actual.used.pct"
              }
            }
          }
        }
      }
    }
  }
}';
        $params = [
            "index" => "metricbeat-*",
            "body" => $json,

        ];

        $host = [
            [
                'host' => 'elasticsearch',
                'port' => '9200',
                'user' => 'elastic',
                'pass' => 'changeme',
            ],
        ];
        $elasticsearch = ClientBuilder::create()
                                      ->setHosts($host)
                                      ->build();

        $response = $elasticsearch->search($params);

        foreach ($response['aggregations']['interval']['buckets']['0']['host']['buckets'] as $host) {

            $history = new History();
            $history->server_host = $host['key'];
            $history->resource = 'memory';
            $history->value = (int)((float)$host['value']['value'] * 100);

            $date = new DateTime();
            $history->timestamp = $date->getTimestamp();

            $lastHistory = HistorySearch::getLastHistoryByHostAndResource($history->server_host, 'memory');

            if ($lastHistory) {
                $now = new DateTime();
                $last = new DateTime();
                $last->setTimestamp($lastHistory->timestamp);

                $diff = $now->diff($last);


                if ($diff->format('%i') > 70) {

                    if ($history->value > 50) {

                        if ($history->validate() && $history->save()) {
                            $resource = $history->resource;
                            $value = $history->value;
                            $server = $history->server_host;
                            $message = Yii::$app->mailer->compose();
                            $message->setFrom('email@gmail.com');
                            $message->setTo('email@gmail.com');
                            $message->setSubject('Critical server values');
                            $message->setTextBody("Reached critical values of ${resource} on server ${server}.\n Value: ${value}\n");
                            if (!$message->send()) {
                                echo "Mail error\n";
                                return;
                            }
                        } else {
                            print_r($history->getErrors());
                        }
                    }
                }
            } else {

                if ($history->value > 70) {
                    if ($history->validate() && $history->save()) {
                        $resource = $history->resource;
                        $value = $history->value;
                        $server = $history->server_host;
                        $message = Yii::$app->mailer->compose();
                        $message->setFrom('email@gmail.com');
                        $message->setTo('email@gmail.com');
                        $message->setSubject('Critical server values');
                        $message->setTextBody("Reached critical values of ${resource} on server ${server}.\n Value: ${value}\n");
                        if (!$message->send()) {
                            echo "Mail error\n";
                            return;
                        }
                    } else {
                        print_r($history->getErrors());
                    }
                }
            }
        }
    }

    public function actionCheckPeaksCpu()
    {

        $json = '{
  "size": 0, 
  "query": {
    "bool": {
      "must": [
        {
          "exists": {
            "field": "system.cpu.total.norm.pct"
          }
        }
      ],
      "filter": [
        {
          "range": {
            "@timestamp": {
              "gte": "now-1m/m",
              "lte": "now"
            }
          }
        }
      ]
    }
  },
  "aggs": {
    "interval": {
      "date_histogram": {
        "field": "@timestamp",
        "fixed_interval": "1m",
        "order": {
          "_key": "desc"
        }
      },
      "aggs": {
        "host": {
          "terms": {
            "field": "host.name.keyword"
          },
          "aggs": {
            "value": {
              "avg": {
                "field": "system.cpu.total.norm.pct"
              }
            }
          }
        }
      }
    }
  }
}';
        $params = [
            "index" => "metricbeat-*",
            "body" => $json,

        ];

        $host = [
            [
                'host' => 'elasticsearch',
                'port' => '9200',
                'user' => 'elastic',
                'pass' => 'changeme',
            ],
        ];
        $elasticsearch = ClientBuilder::create()
                                      ->setHosts($host)
                                      ->build();

        $response = $elasticsearch->search($params);


        foreach ($response['aggregations']['interval']['buckets']['0']['host']['buckets'] as $host) {

            $history = new History();
            $history->server_host = $host['key'];
            $history->resource = 'cpu';
            $history->value = (int)((float)$host['value']['value'] * 100);

            $date = new DateTime();
            $history->timestamp = $date->getTimestamp();

            $lastHistory = HistorySearch::getLastHistoryByHostAndResource($history->server_host, 'cpu');

            if ($lastHistory) {
                $now = new DateTime();
                $last = new DateTime();
                $last->setTimestamp($lastHistory->timestamp);

                $diff = $now->diff($last);

                if ($diff->format('%i') > 10) {

                    if ($history->value > 60) {
                        if ($history->validate() && $history->save()) {
                            $resource = $history->resource;
                            $value = $history->value;
                            $server = $history->server_host;
                            $message = Yii::$app->mailer->compose();
                            $message->setFrom('email@gmail.com');
                            $message->setTo('email@gmail.com');
                            $message->setSubject('Critical server values');
                            $message->setTextBody("Reached critical values of ${resource} on server ${server}.\n Value: ${value}\n");
                            if (!$message->send()) {
                                echo "Mail error\n";
                                return;
                            }
                        } else {
                            print_r($history->getErrors());
                        }
                    }
                }
            } else {

                if ($history->value > 60) {
                    if ($history->validate() && $history->save()) {
                        $resource = $history->resource;
                        $value = $history->value;
                        $server = $history->server_host;
                        $message = Yii::$app->mailer->compose();
                        $message->setFrom('email@gmail.com');
                        $message->setTo('email@gmail.com');
                        $message->setSubject('Critical server values');
                        $message->setTextBody("Reached critical values of ${resource} on server ${server}.\n Value: ${value}\n");
                        if (!$message->send()) {
                            echo "Mail error\n";
                            return;
                        }
                    } else {
                        print_r($history->getErrors());
                    }
                }
            }
        }
    }
}
