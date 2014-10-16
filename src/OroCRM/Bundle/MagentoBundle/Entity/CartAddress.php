<?php

namespace OroCRM\Bundle\MagentoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\AddressBundle\Model\PhoneHolderInterface;
use Oro\Bundle\DataAuditBundle\Metadata\Annotation as Oro;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use OroCRM\Bundle\MagentoBundle\Model\ExtendCartAddress;

/**
 * @ORM\Table("orocrm_magento_cart_address")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *       defaultValues={
 *          "entity"={
 *              "icon"="icon-map-marker"
 *          },
 *          "note"={
 *              "immutable"=true
 *          },
 *          "activity"={
 *              "immutable"=true
 *          },
 *          "attachment"={
 *              "immutable"=true
 *          }
 *      }
 * )
 * @ORM\Entity
 * @Oro\Loggable
 */
class CartAddress extends ExtendCartAddress implements PhoneHolderInterface
{
    use OriginTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    protected $phone;

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryPhoneNumber()
    {
        return !empty($this->phone) ? $this->phone : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhoneNumbers()
    {
        $phones = [];

        if (!empty($this->phone)) {
            $phones[] = $this->phone;
        }

        return $phones;
    }
}
