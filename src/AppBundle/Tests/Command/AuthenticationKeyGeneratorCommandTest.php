<?php

namespace AppBundle\Tests\Command;

use AppBundle\Tests\AbstractTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use AppBundle\Command\AuthenticationKeyGeneratorCommand;

/**
 * AuthenticationKeyGeneratorCommandTest - Unit test for AuthenticationKeyGeneratorCommand
 * @covers AuthenticationKeyGeneratorCommand
 */
class AuthenticationKeyGeneratorCommandTest extends AbstractTestCase {

    /**
     * Tests AuthenticationKeyGeneratorCommand
     * Tests valid case where no update expected for authentication keys if new, or updated if old
     * @covers AuthenticationKeyGeneratorCommand
     * 
     * @dataProvider provideDataForAuthenticationKeyGeneratorCommandTestCases
     * 
     * Assert private key modification date updated successfully
     * Assert public key modification date updated successfully
     * Assert private key not updated by command if new, or updated if old
     * Assert public key not updated by command if new, or updated if old
     */
    public function testAuthenticationKeyGeneratorCommand($lastModificationTime, $keysUpdateAssertMethod) 
    {
        $authenticationKeysBeforeUpdate = $this->getAuthenticationKeys();
        // Assert private key modification date updated successfully
        // Assert public key modification date updated successfully
        $this->updateAuthenticationKeysLastModificationDate($lastModificationTime);

        // testing the command part
        // init kernel, required to init Application
        $kernel = self::createKernel();
        $kernel->boot();
        // init Application
        $application = new Application($kernel);
        // add our tested command to the new Application
        $application->add(new AuthenticationKeyGeneratorCommand());

        // lookup the command name and excute
        $command = $application->find('rest:generateAuthKey');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));
        
        $authenticationKeysAfterUpdate = $this->getAuthenticationKeys();

        // Assert private key not updated by command as it is new to be updated
        $this->{$keysUpdateAssertMethod}($authenticationKeysBeforeUpdate['private'], $authenticationKeysAfterUpdate['private']);
        // Assert public key not updated by command as it is new to be updated
        $this->{$keysUpdateAssertMethod}($authenticationKeysBeforeUpdate['public'], $authenticationKeysAfterUpdate['public']);
    }
        
    /**
      * prepare data to be used as providers for test cases
      * prepare data needed for authentication key generator command test cases for valid cases where key can be updated if old, or not if new
      */
    public function provideDataForAuthenticationKeyGeneratorCommandTestCases()
    {

        return array(
          array(/*$lastModificationTime*/false, /*$keysUpdateAssertMethod*/'assertEquals'),
          array(/*$lastModificationTime*/time()- (30 * 356 * 24 * 60 * 60), /*$keysUpdateAssertMethod*/'assertNotEquals'),
        );
    }
    
    
    /**
     * get authentication public and private keys
     * 
     * @return array public and private keys array
     */
    private function getAuthenticationKeys() 
    {
        $privateKeyPath = $this->container->getParameter('RestApiAuthentication.privateKeyPath');
        $publicKeyPath = $this->container->getParameter('RestApiAuthentication.publicKeyPath');
        $privateKey =  file_get_contents($privateKeyPath);
        $publicKey = file_get_contents($publicKeyPath);
        
        return array(
            'public' => $publicKey,
            'private' => $privateKey
        );
    }
    
    /**
     * update authentication public and private keys last modification date
     * if time is not supplied, files will be updated with current system time
     * 
     * @param  mixed  $lastModificationTime last modification timestamp ,default is boolean false where files are updated with current time
     * 
     * Assert private key modification date updated successfully
     * Assert public key modification date updated successfully
     */
    private function updateAuthenticationKeysLastModificationDate($lastModificationTime = false) 
    {
        $privateKeyPath = $this->container->getParameter('RestApiAuthentication.privateKeyPath');
        $publicKeyPath = $this->container->getParameter('RestApiAuthentication.publicKeyPath');
        
        if($lastModificationTime !== false)
        {
            $privateKeyModificationDateUpdateResult = touch($privateKeyPath, $lastModificationTime);
            $publicKeyModificationDateUpdateResult = touch($publicKeyPath, $lastModificationTime);
        }
        else 
        {
            $privateKeyModificationDateUpdateResult = touch($privateKeyPath);
            $publicKeyModificationDateUpdateResult = touch($publicKeyPath);
        }
        
        // Assert private key modification date updated successfully
        $this->assertTrue($privateKeyModificationDateUpdateResult);
        
        // Assert public key modification date updated successfully
        $this->assertTrue($publicKeyModificationDateUpdateResult);
    }
}
