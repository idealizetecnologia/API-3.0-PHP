<?php
namespace Cielo\API30\Ecommerce;

class Bin implements \JsonSerializable
{

    private $CardType;
    private $CorporateCard;
    private $ForeignCard;
    private $Issuer;
    private $IssuerCode;
    private $Provider;
    private $Status;

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public static function fromJson($json)
    {
        $bin = new Bin();
        $bin->populate(json_decode($json));

        return $bin;
    }

    public function populate(\stdClass $data)
    {
        $reflect = new \ReflectionObject($data);
        $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);
        foreach ($props as $prop) {
            try {
                $this->{$prop->getName()} = $data->{$prop->getName()};
            } catch (\Exception $e) {
            }
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCardType()
    {
        return $this->CardType;
    }

    /**
     * @param mixed $CardType
     * @return Bin
     */
    public function setCardType($CardType)
    {
        $this->CardType = $CardType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCorporateCard()
    {
        return $this->CorporateCard;
    }

    /**
     * @param mixed $CorporateCard
     * @return Bin
     */
    public function setCorporateCard($CorporateCard)
    {
        $this->CorporateCard = $CorporateCard;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getForeignCard()
    {
        return $this->ForeignCard;
    }

    /**
     * @param mixed $ForeignCard
     * @return Bin
     */
    public function setForeignCard($ForeignCard)
    {
        $this->ForeignCard = $ForeignCard;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIssuer()
    {
        return $this->Issuer;
    }

    /**
     * @param mixed $Issuer
     * @return Bin
     */
    public function setIssuer($Issuer)
    {
        $this->Issuer = $Issuer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIssuerCode()
    {
        return $this->IssuerCode;
    }

    /**
     * @param mixed $IssuerCode
     * @return Bin
     */
    public function setIssuerCode($IssuerCode)
    {
        $this->IssuerCode = $IssuerCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProvider()
    {
        return $this->Provider;
    }

    /**
     * @param mixed $Provider
     * @return Bin
     */
    public function setProvider($Provider)
    {
        $this->Provider = $Provider;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->Status;
    }

    /**
     * @param mixed $Status
     * @return Bin
     */
    public function setStatus($Status)
    {
        $this->Status = $Status;
        return $this;
    }

}
