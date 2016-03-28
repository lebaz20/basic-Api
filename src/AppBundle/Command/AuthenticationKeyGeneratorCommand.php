<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use DateTime;

/**
 * AuthenticationKeyGeneratorCommand - create new private and public keys, used to generate authentication token used by REST API
 * keys renewal adds a lot to our REST API security, but too much renewal will increase the frequency of Basic HTTP Authentication, so a balance is required
 * 
 */
class AuthenticationKeyGeneratorCommand extends ContainerAwareCommand {

     /**
     * configure command name and description
     * 
     * 
     * 
     */
    protected function configure()
    {
        $this
                ->setName('rest:generateAuthKey')
                ->setDescription('Create new private and public keys, used to generate authentication token used by REST API')
        ;
    }

    /**
     * execute command of creating autentication keys
     * 
     * 
     * 
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @throws \Exception
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        try{
            $errors = array();
             
            // get minimum acceptable date ,older keys should be renewed ,newer keys should not
            $keyLifeTimeInDays = $this->getContainer()->getParameter('RestApiAuthentication.keyLifePeriodInDays');
            $firstAcceptableModificationDate = new DateTime();
            $firstAcceptableModificationDateInterval = new \DateInterval("P".$keyLifeTimeInDays."D");
            $firstAcceptableModificationDate->sub($firstAcceptableModificationDateInterval);
    
            // get keys last modification date
            $privateKeyPath = $this->getContainer()->getParameter('RestApiAuthentication.privateKeyPath');
            $publicKeyPath = $this->getContainer()->getParameter('RestApiAuthentication.publicKeyPath');
            $privateKeyLastModifiedTimestamp = filemtime ( /*$filename =*/ $privateKeyPath );
            $publicKeyLastModifiedTimestamp = filemtime ( /*$filename =*/ $publicKeyPath );
            if ($privateKeyLastModifiedTimestamp !== false && $publicKeyLastModifiedTimestamp !== false )
            {
                $privateKeyLastModifiedDate = new DateTime('@' . $privateKeyLastModifiedTimestamp);
                $publicKeyLastModifiedDate = new DateTime('@' . $publicKeyLastModifiedTimestamp);
                if($privateKeyLastModifiedDate <= $firstAcceptableModificationDate && $publicKeyLastModifiedDate <= $firstAcceptableModificationDate)
                {
                    $digestAlg = $this->getContainer()->getParameter('RestApiAuthentication.digestAlg');
                    $privateKeyBits = $this->getContainer()->getParameter('RestApiAuthentication.privateKeyBits');
                    $config = array(
                        "digest_alg" => $digestAlg,
                        "private_key_bits" => $privateKeyBits,
                        "private_key_type" => OPENSSL_KEYTYPE_RSA,
                        "encrypt_key" => true,
                        "encrypt_key_cipher" => OPENSSL_CIPHER_AES_256_CBC
                    );

                    // Create the private and public key
                    $resource = openssl_pkey_new($config);
                    if($resource === false)
                    {
                        throw new \Exception("Creating Resource for keys failed");
                    }

                    $passPhrase = $this->getContainer()->getParameter('RestApiAuthentication.passPhrase');
                    // Extract the private key from $resource to $privateKey
                    $resourceExtractResult = openssl_pkey_export($resource, $privateKey, $passPhrase);
                    if($resourceExtractResult === false)
                    {
                        throw new \Exception("Extracting Private Key from keys' Resource failed");
                    }
                    // Extract the public key from $resource to $publicKey
                    $publicKeyArray = openssl_pkey_get_details($resource);
                    if($publicKeyArray === false)
                    {
                        throw new \Exception("Extracting Public Key from keys' Resource failed");
                    }
                    $publicKey = $publicKeyArray["key"];


                    $privateKeyFileCreationResult = \file_put_contents($privateKeyPath, $privateKey);
                    if($privateKeyFileCreationResult === false)
                    {
                        throw new \Exception("Saving private key in path : ".$privateKeyPath." failed");
                    }

                    $publicKeyFileCreationResult = \file_put_contents($publicKeyPath, $publicKey);
                    if($publicKeyFileCreationResult === false)
                    {
                        throw new \Exception("Saving public key in path : ".$privateKeyPath." failed");
                    }
                }
            }
        } 
        catch (\Exception $e) 
        {
            // add error to collected ones to send via email
            $errors[] = $e->getMessage().PHP_EOL.$e->getTraceAsString();
            // display error message to user
            $output->writeln("{$e->getMessage()} {$e->getCode()}");
        }
    }

}
