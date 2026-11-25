<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ViewHistoryControllerTest extends WebTestCase
{
    public function testAnonymousUserCannotRecordView(): void
    {
        // Arrange(準備):ブラウザの代わりになるクライアントを作る
        $client = static::createClient();

        // Act(実行):ログインせずに、閲覧履歴を記録しようとする
        $client->request(
            'POST',
            '/api/history',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['recipeId' => 1])
        );

        // Assert(検証):ログインページにリダイレクトされるはず
        $this->assertResponseRedirects('/login');
    }
}