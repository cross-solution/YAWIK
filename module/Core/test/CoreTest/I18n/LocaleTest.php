<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.26
 */
namespace CoreTest\I18n;

use PHPUnit\Framework\TestCase;

use Core\I18n\Locale as LocaleService;
use Zend\Http\Request;
use Auth\Entity\UserInterface as User;

class LocaleTest extends TestCase
{

    /**
     * @var array
     */
    protected $languages = [
        'de' => 'de_DE',
        'fr' => 'fr',
        'en' => 'en_US',
        'es' => 'es',
        'it' => 'it'
    ];
    
    /**
     * @var LocaleService
     */
    protected $localeService;
    
    /**
     * @see PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->localeService = new LocaleService($this->languages);
    }

    public function testGetDefaultLanguage()
    {
        $expected = array_keys($this->languages);
        $expected = current($expected);
        $this->assertSame($expected, $this->localeService->getDefaultLanguage());
    }

    public function testIsLanguageSupported()
    {
        foreach (array_keys($this->languages) as $language) {
            $this->assertTrue($this->localeService->isLanguageSupported($language));
        }
        
        $this->assertFalse($this->localeService->isLanguageSupported('nonExistent'));
    }

    public function testGetLocaleByLanguage()
    {
        foreach ($this->languages as $language => $locale) {
            $this->assertSame($locale, $this->localeService->getLocaleByLanguage($language));
        }
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported language');
        $this->localeService->getLocaleByLanguage('nonExistent');
    }
    
    public function testDetectLanguageWithoutUser()
    {
        $expected = 'es';
        $request = new Request();
        $request->getHeaders()->addHeaderline('Accept-Language', $expected);
        
        $this->assertSame($expected, $this->localeService->detectLanguage($request));
        
        $expected = 'nonExistent';
        $request = new Request();
        $request->getHeaders()->addHeaderline('Accept-Language', $expected);
        
        $this->assertSame($this->localeService->getDefaultLanguage(), $this->localeService->detectLanguage($request));
    }
    
    public function testDetectLanguageWithUserWithoutSettings()
    {
        $expected = 'es';
        $request = new Request();
        $request->getHeaders()->addHeaderline('Accept-Language', $expected);
        
        $settings = new \stdClass();
        
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->once())
            ->method('getSettings')
            ->with($this->equalTo('Core'))
            ->willReturn($settings);
        
        $this->assertSame($expected, $this->localeService->detectLanguage($request, $user));
    }
    
    public function testDetectLanguageWithUserWithSettings()
    {
        $expected = 'it';
        
        $request = $this->getMockBuilder(Request::class)
            ->setMethods(['getHeaders'])
            ->getMock();
        $request->expects($this->never())
            ->method('getHeaders');
        
        $settings = new \stdClass();
        $settings->localization = new \stdClass();
        $settings->localization->language = $expected;
        
        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->once())
            ->method('getSettings')
            ->with($this->equalTo('Core'))
            ->willReturn($settings);
        
        $this->assertSame($expected, $this->localeService->detectLanguage($request, $user));
    }
}
