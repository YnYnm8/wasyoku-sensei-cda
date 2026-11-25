<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginPageTest extends WebTestCase
{
    public function testLoginPageIsAccessible(): void
    {
        // Arrange(準備):ブラウザの代わりになるクライアント
        $client = static::createClient();

        // Act(実行)：
        $client->request(
            'GET',
            '/loginXYZ'
           
        );

        // // Assert(検証):ログインページが正常に表示される(成功の返事が返る)はず
        $this->assertResponseIsSuccessful('Login page should be accessible');
    }
}
