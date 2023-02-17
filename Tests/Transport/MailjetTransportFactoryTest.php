<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Bridge\Mailjet\Tests\Transport;

use Symfony\Component\Mailer\Bridge\Mailjet\Transport\MailjetApiTransport;
use Symfony\Component\Mailer\Bridge\Mailjet\Transport\MailjetSmtpTransport;
use Symfony\Component\Mailer\Bridge\Mailjet\Transport\MailjetTransportFactory;
use Symfony\Component\Mailer\Test\TransportFactoryTestCase;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\TransportFactoryInterface;

class MailjetTransportFactoryTest extends TransportFactoryTestCase
{
    public static function getFactory(): TransportFactoryInterface
    {
        return new MailjetTransportFactory(self::getDispatcher(), self::getClient(), self::getLogger());
    }

    public static function supportsProvider(): iterable
    {
        yield [
            new Dsn('mailjet+api', 'default'),
            true,
        ];

        yield [
            new Dsn('mailjet', 'default'),
            true,
        ];

        yield [
            new Dsn('mailjet+smtp', 'default'),
            true,
        ];

        yield [
            new Dsn('mailjet+smtps', 'default'),
            true,
        ];

        yield [
            new Dsn('mailjet+smtp', 'example.com'),
            true,
        ];
    }

    public static function createProvider(): iterable
    {
        $dispatcher = self::getDispatcher();
        $logger = self::getLogger();

        yield [
            new Dsn('mailjet+api', 'default', self::USER, self::PASSWORD),
            new MailjetApiTransport(self::USER, self::PASSWORD, self::getClient(), $dispatcher, $logger),
        ];

        yield [
            new Dsn('mailjet+api', 'example.com', self::USER, self::PASSWORD),
            (new MailjetApiTransport(self::USER, self::PASSWORD, self::getClient(), $dispatcher, $logger))->setHost('example.com'),
        ];

        yield [
            new Dsn('mailjet', 'default', self::USER, self::PASSWORD),
            new MailjetSmtpTransport(self::USER, self::PASSWORD, $dispatcher, $logger),
        ];

        yield [
            new Dsn('mailjet+smtp', 'default', self::USER, self::PASSWORD),
            new MailjetSmtpTransport(self::USER, self::PASSWORD, $dispatcher, $logger),
        ];

        yield [
            new Dsn('mailjet+smtps', 'default', self::USER, self::PASSWORD),
            new MailjetSmtpTransport(self::USER, self::PASSWORD, $dispatcher, $logger),
        ];
    }

    public static function unsupportedSchemeProvider(): iterable
    {
        yield [
            new Dsn('mailjet+foo', 'mailjet', self::USER, self::PASSWORD),
            'The "mailjet+foo" scheme is not supported; supported schemes for mailer "mailjet" are: "mailjet", "mailjet+api", "mailjet+smtp", "mailjet+smtps".',
        ];
    }

    public static function incompleteDsnProvider(): iterable
    {
        yield [new Dsn('mailjet+smtp', 'default')];
    }
}
