<?php

namespace mmFaker\Generators;

use mmFaker\Faker;

trait IPAddressTrait
{

    /**
     * Add an IP address to the fields
     *
     * @param  string $fieldName The field name
     * @param int $generationMode Faker::FIXED_VALUE or Faker::RANDOM_VALUE
     * @param  bool $ipv4 TRUE if the address must be IPv4 (if IPv6 is also true, return a random IP type 4/6)
     * @param  bool $ipv6 TRUE if the address must be IPv6 (if IPv4 is also true, return a random IP type 4/6)
     * @param  string $fixedValue Fixed value for FIXED_VALUE generation mode
     * @return Faker The $this class reference for chain-of
     */
    public function addIPAddress(string $fieldName, int $generationMode = Faker::RANDOM_VALUE, bool $ipv4 = true, bool $ipv6 = false, string $fixedValue = null): Faker
    {
        $this->columns[$fieldName] = array(
            "type"      => Faker::TYPE_IPADDRESS,
            "random"    => $generationMode == Faker::RANDOM_VALUE ? true : false,
            "ipv4"      => $generationMode == Faker::RANDOM_VALUE ? $ipv4 : true,
            "ipv6"      => $generationMode == Faker::RANDOM_VALUE ? $ipv6 : true,
            "fixValue"  => $fixedValue,
        );
        return $this;
    }

    /**
     * Generate an IP address
     *
     * @param  bool $ipv4 TRUE if the address must be IPv4 (if IPv6 is also true, return a random IP type 4/6)
     * @param  bool $ipv6 TRUE if the address must be IPv6 (if IPv4 is also true, return a random IP type 4/6)
     * @return string An IP address
     */
    protected function generateIPAddress($ipv4 = true, $ipv6 = false): string
    {
        switch (true) {
            case ($ipv4 && $ipv6):
                if (rand(0, 1) == 1) {
                    // IPV6
                    return str_replace(array(':::', '::::'), '::', str_replace(':0:', '::', implode(':', array(dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535))))));
                } else {
                    // IPV4
                    return implode('.', array(rand(0, 220), rand(0, 255), rand(0, 255), rand(0, 254)));
                }
                break;
            case ($ipv4):
                return implode('.', array(rand(0, 220), rand(0, 255), rand(0, 255), rand(0, 254)));
                break;
            case ($ipv6):
                return str_replace(array(':::', '::::'), '::', str_replace(':0:', '::', implode(':', array(dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535))))));
                break;
        } // switch
    }
}
