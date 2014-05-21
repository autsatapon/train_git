<?php

use Elastica\Client as ElasticaClient;
use Elastica\Document as ElasticDocument;

class ApiDocumentController extends \BaseController {

    protected $client;

    protected $document;

    public function __construct(ElasticaClient $client, ElasticDocument $document)
    {
        $this->client = $client;

        $this->document = $document;

        // Set Config for Elastica Client
        $this->client->setConfig(array(
            'host' => Config::get('elastica.host'),
            'port' => Config::get('elastica.port')
        ));

        $this->client->getConnection()->setHost(Config::get('elastica.host'))->setPort(Config::get('elastica.port'));
    }



    public function store($index, $type)
    {
        $indexType = $this->client->getIndex($index)->getType($type);

        $data = Input::all();

        $id = $data['id'];

        $data['published_at'] = str_replace(' ', 'T', $data['published_at']);
        $data['created_at'] = str_replace(' ', 'T', $data['created_at']);
        $data['updated_at'] = str_replace(' ', 'T', $data['updated_at']);

        if (!empty($data['variants']))
        {
            foreach ($data['variants'] as $k1=>$v1)
            {
                if (!empty($v1['active_special_discount']['started_at']))
                {
                    $data['variants'][$k1]['active_special_discount']['started_at'] = str_replace(' ', 'T', $v1['active_special_discount']['started_at']);
                }

                if (!empty($v1['active_special_discount']['ended_at']))
                {
                    $data['variants'][$k1]['active_special_discount']['ended_at'] = str_replace(' ', 'T', $v1['active_special_discount']['ended_at']);
                }
            }
        }

        $document = $this->document->setId($id)->setData($data);

        $indexType->addDocument($document);
        $indexType->getIndex()->refresh();

        return API::createResponse($data, 201);
    }



    public function update($index, $type, $id)
    {
        $indexType = $this->client->getIndex($index)->getType($type);

        $data = Input::all();

        $data['published_at'] = str_replace(' ', 'T', $data['published_at']);
        $data['created_at'] = str_replace(' ', 'T', $data['created_at']);
        $data['updated_at'] = str_replace(' ', 'T', $data['updated_at']);

        if (!empty($data['variants']))
        {
            foreach ($data['variants'] as $k1=>$v1)
            {
                if (!empty($v1['active_special_discount']['started_at']))
                {
                    $data['variants'][$k1]['active_special_discount']['started_at'] = str_replace(' ', 'T', $v1['active_special_discount']['started_at']);
                }

                if (!empty($v1['active_special_discount']['ended_at']))
                {
                    $data['variants'][$k1]['active_special_discount']['ended_at'] = str_replace(' ', 'T', $v1['active_special_discount']['ended_at']);
                }
            }
        }

        $document = $this->document->setId($id)->setData($data);

        $indexType->addDocument($document);
        $indexType->getIndex()->refresh();

        return API::createResponse($data, 200);

    }



    public function destroy($index, $type, $id)
    {
        $inputs = array(
            'index' => $index,
            'type'  => $type,
            'id'    => $id
        );

        $validator = Validator::make($inputs, array(
            'index' => 'required',
            'type'  => 'required|alpha_num',
            'id'    => 'required|numeric'
        ));

        if ($validator->fails())
        {
            $errors = $validator->messages()->all(':message');

            return API::createResponse(compact('errors'), 400);
        }

        $indexType = $this->client->getIndex($index)->getType($type);

        try
        {
            $indexType->deleteById($id);
        }
        catch (Exception $e) { }

        $indexType->getIndex()->refresh();

        return API::createResponse(null, 204);
    }

}