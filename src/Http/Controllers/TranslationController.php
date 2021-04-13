<?php

namespace Canvas\Http\Controllers;


use Canvas\Http\Requests\TranslationRequest;
use Google\Cloud\Translate\V2\TranslateClient;
use Illuminate\Routing\Controller;

class TranslationController extends Controller
{
    private $translate;

    public function __construct() {
        if (config('canvas.google_translate.access_key')) {
            $this->translate = new TranslateClient([
                'key' => config('canvas.google_translate.access_key')
            ]);
        }
    }

    private function getTranslation($content, $lang)
    {
        $translation = $this->translate->translate($content, [
            'target' => $lang
        ]);

        return $translation['text'];
    }

    public function translate(TranslationRequest $request): array
    {
        $data = $request->validated();
        $language = $data['language'];

        if (isset($data['title'])) { //post, portfolio
            $data['title'] = $this->getTranslation($data['title'], $language);
            if (isset($data['summary'])) {
                $data['summary'] = $this->getTranslation($data['summary'], $language);
            }
            if (isset($data['body'])) {
                $data['body'] = $this->getTranslation($data['body'], $language);
            }

            if (isset($data['info'])) { //portfolio
                if (isset($data['info']['project_name'])) {
                    $data['info']['project_name'] = $this->getTranslation($data['info']['project_name'], $language);
                }
                if (isset($data['info']['location'])) {
                    $data['info']['location'] = $this->getTranslation($data['info']['location'], $language);
                }
            }

            if (isset($data['meta']['description'])) {
                $data['meta']['description'] = $this->getTranslation($data['meta']['description'], $language);
            }
            if (isset($data['meta']['title'])) {
                $data['meta']['title'] = $this->getTranslation($data['meta']['title'], $language);
            }
            if (isset($data['meta']['canonical_link'])) {
                $data['meta']['canonical_link'] = $this->getTranslation($data['meta']['canonical_link'], $language);
            }

        } else if(isset($data['name'])) { //category, tag, topic
            $data['name'] = $this->getTranslation($data['name'], $language);
        }

        return $data;

    }

}