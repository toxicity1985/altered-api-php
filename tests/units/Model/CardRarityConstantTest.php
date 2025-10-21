<?php

namespace Toxicity\AlteredApi\tests\units\Model;

use atoum\atoum;

class CardRarityConstantTest extends atoum\test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Model\CardRarityConstant';
    }

    public function testConstants()
    {
        $class = 'Toxicity\AlteredApi\Model\CardRarityConstant';
        
        $this
            ->boolean(class_exists($class))
                ->isTrue()
        ;

        // Test que les constantes peuvent être accédées si la classe existe
        if (class_exists($class)) {
            $reflection = new \ReflectionClass($class);
            $constants = $reflection->getConstants();
            
            $this
                ->array($constants)
                    ->isNotEmpty()
            ;
        }
    }
}

