<?php
namespace Incloud\Recaptcha\Tests\Functional\Domain\Model;

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Verify the functionality of the validator
 */
class RecaptchaTest extends \TYPO3\FLOW3\Tests\FunctionalTestCase {

	/**
	 * @var boolean
	 */
	static protected $testablePersistenceEnabled = false;
	
	/*
	 * @var \Incloud\Recaptcha\Domain\Model\Recaptcha
	 */
	protected $recaptcha;
	
	/*
	 * @var \TYPO3\FLOW3\Session\SessionInterface
	 */
	protected $session;
	
	
	/**
	 *  Initialize recaptcha
	 *  
	 *  @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->recaptcha = new \Incloud\Recaptcha\Domain\Model\Recaptcha;
		$this->session = $this->objectManager->get('TYPO3\FLOW3\Session\SessionInterface');
		if(!$this->session->isStarted())
			$this->session->start();
	}
	
	/**
	 * @test
	 */
	public function testRememberFunctionality() {
		$this->session->putData('recaptcha_timestamp', time());
		$res = $this->recaptcha->isRemembered();
		$this->assertTrue($res);
		
		$this->session->putData('recaptcha_timestamp', 0);
		$res = $this->recaptcha->isRemembered();
		$this->assertFalse($res);
	}
	
	/**
	 * @test
	 */
	public function testInvalidateFunctionality() {
		$res = $this->recaptcha->invalidate();
		$res = $this->recaptcha->isRemembered();
		$this->assertFalse($res);
		
		$this->session->putData('recaptcha_timestamp', time());
		$res = $this->recaptcha->invalidate();
		$res = $this->recaptcha->isRemembered();
		$this->assertFalse($res);
	}
	
	/**
	 * Since google's reCaptcha works with dynamic challenges, we are unable to test a valid challenge-response pair.
	 * So we can just proof that an invalid challenge-response pair will not evaluate to true.
	 * 
	 * @test
	 */
	public function testValidationFunctionality() {
		$res = $this->recaptcha->validate("challenge", "response");
		$this->assertInternalType('string', $res, "Something went terribly wrong. Recaptcha validates true without valid data!");	
	}
	
	/**
	 * @test
	 */
	public function testRememberedValidationFunctionality() {
		$this->session->putData('recaptcha_timestamp', time());
		$res = $this->recaptcha->validate("challenge", "response", true);
		$this->assertTrue($res);
	}
	
}