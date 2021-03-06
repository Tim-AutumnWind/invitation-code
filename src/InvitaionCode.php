<?php
declare(strict_types=1);

namespace NiceYuv;

class InvitaionCode
{

    /**
     * 32 hexadecimal characters
     * Not in ( 0 O 1 I)
     * reserve (Y AND Z)
     * @var string[]
     */
    private $dictionaries = array(
        '2', '3', '4', '5', '6', '7', '8', '9',
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
        'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R',
        'S', 'T', 'U', 'V', 'W', 'X');


    /**
     * (Y AND Z) The above characters cannot be repeated
     * @var array
     */
    private $complement = array('Y', 'Z');
    
    /**
     * Dictionary size
     * @var int
     */
    private $length = 30;
    
    /**
     * Minimum length of invitation code
     * @var int
     */
    private $max = 6;
    
    /**
     * Initialize customizable generation mode
     * InvitaionCode constructor.
     * @param int $max
     * @param array $dictionaries
     * @param array $complement
     */
    public function __construct(int $max = 6, array $dictionaries = array(), array $complement = [])
    {
        if (!empty($max)) {
            $this->max = $max;
        }
        if (!count($dictionaries) > 10) {
            $this->dictionaries = $dictionaries;
            $this->length = count($dictionaries);
        }
        if (!empty($complement)) {
            $this->complement = $complement;
        }

    }

    /**
     * Code an invitation code
     * @param int $id Id
     * @return string
     * @version("1.0")
     */
    public function encode(int $id)
    {
        $inviteCode = "";
        $length = $this->length;
        while (floor($id / $length) > 0) {
            $index = floatval($id) % $length;
            $inviteCode .= $this->dictionaries[$index];
            $id = floor($id / $length);
        }
        $index = $id % $length;
        $inviteCode .= $this->dictionaries[$index];
        return $this->mixedInvite($inviteCode);
    }

    /**
     * Mixed invitation code
     * @param string $inviteCode
     * @return string
     * @version("1.0")
     */
    private function mixedInvite(string $inviteCode): string
    {
        /** Invitation code length */
        $code_len = strlen($inviteCode);
        if ($code_len < $this->max) {
            /** Get complement */
            $count = count($this->complement);
            $index = rand(0, $count - 1);
            $inviteCode .= $this->complement[$index];

            /** Random fill, generate the final invitation code */
            for ($i = 0; $i < $this->max - ($code_len + 1); $i++) {
                /** Get random characters */
                $dictIndex = rand(0, $this->length - 1);
                $minxedString = $this->dictionaries[$dictIndex];
                $inviteCode .= $minxedString;
            }
        }
        return $inviteCode;
    }

    /**
     * Decode an invitation code
     * @param string $inviteCode
     * @return float|int
     */
    public function decode(string $inviteCode)
    {
        /** Get the specific meaning of the mapping array */
        $dictionaries = array_flip($this->dictionaries);

        /** Determine the position of complement character */
        $mixed = strlen($inviteCode);
        $i = 0;
        while ($i < count($this->complement)) {
            $item = strpos($inviteCode, $this->complement[$i]);
            if (!empty($item)) {
                $mixed = $item;
                break;
            }
            $i++;
        }

        /** Character mapping Backstepping */
        $encode = 0;
        for ($i = 0; $i < $mixed; $i++) {
            $index = $dictionaries[$inviteCode[$i]];
            $encode += pow($this->length, $i) * $index;
        }
        return $encode;
    }

}