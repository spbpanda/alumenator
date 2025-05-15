<?php

namespace PhonePe\payments\v1\models\response\PaymentInstrument;

use PhonePe\payments\v1\constants\PaymentInstrumentConstants;
use PhonePe\payments\v1\models\response\PaymentInstrument;

class CardPaymentInstrument extends PaymentInstrument implements \JsonSerializable
{
    /**
     * @var RedirectInfo
     */
     private $redirectInfo;

    /**
     * @param RedirectInfo $redirectInfo
     */
    public function __construct(RedirectInfo $redirectInfo)
    {
        parent::__construct(PaymentInstrumentConstants::CARD);
        $this->redirectInfo = $redirectInfo;
    }

    /**
     * @return mixed
     */
    public function getRedirectInfo(): RedirectInfo
    {
        return $this->redirectInfo;
    }

    /**
     * @param mixed $redirectInfo
     */
    public function setRedirectInfo(RedirectInfo $redirectInfo)
    {
        $this->redirectInfo = $redirectInfo;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this) + parent::jsonSerialize();
    }

}
