<?php

namespace PatchManager\Handler;

use PatchManager\OperationData;
use PatchManager\Patchable;
use PatchManager\PatchOperationHandler;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DataHandler implements PatchOperationHandler
{
    protected $magicCall = false;

    /**
     * @param mixed $magicCall
     */
    public function useMagicCall($magicCall)
    {
        $this->magicCall = $magicCall;
    }

    /**
     * @param Patchable $patchable
     * @param OperationData $operationData
     */
    public function handle(Patchable $patchable, OperationData $operationData)
    {
        $pa = new PropertyAccessor($this->magicCall);
        $pa->setValue(
            $patchable,
            $operationData->get('property')->get(),
            $operationData->get('value')->get()
        );
    }

    /**
     * the operation name
     *
     * @return string
     */
    public function getName()
    {
        return 'data';
    }

    /**
     * use the OptionResolver instance to configure the required and optional fields that needs to be passed
     * with the request body. See http://symfony.com/doc/current/components/options_resolver.html to check all
     * possible options
     *
     * @param OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired(array('property', 'value'));
    }
}