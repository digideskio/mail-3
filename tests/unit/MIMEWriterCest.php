<?php

namespace Kodus\Mail\Test\Unit;

use Kodus\Mail\Message;
use Kodus\Mail\Test\TestMessageFactory;
use UnitTester;

/**
 * NOTE: running this test-suite will produce files in the `_output` folder named `mail.*.txt`,
 *       e.g. one file for each successful test.
 *
 *       All MIME messages have been linted/validated using this online validator:
 *
 *           http://www.mimevalidator.net/index.html
 *
 * I'd like to eventually automate MIME validation (and preferably locally) but haven't found the tool to do it.
 */
class MIMEWriterCest
{
    private $last_mime;

    /**
     * @var TestMessageFactory
     */
    private $factory;

    public function __construct()
    {
        $this->factory = new TestMessageFactory();
    }

    public function _after(UnitTester $I)
    {
        $I->dumpFile($this->last_mime);
    }

    public function writeTextMessage(UnitTester $I)
    {
        $message = $this->factory->createTextMessage();

        $quoted_body = TestMessageFactory::TEXT_BODY_QUOTED_PRINTABLE;

        $expected_mime = <<<MIME
Date: Thu, 15 Sep 2016 17:20:54 +0200
To: =?UTF-8?Q?Rasmus =C3=A5h Schultz?= <blip@test.org>
From: blub@test.org
Sender: someone-else@test.org
Subject: =?UTF-8?Q?Hey, Rasmus! I like =C3=86=C3=98=C3=85=C3=A6=C3=B8=C3=A5!?=
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

{$quoted_body}

MIME;

        $I->assertSame($expected_mime, $this->toMIME($message));
    }

    public function writeTextMessageWithAttachment(UnitTester $I)
    {
        $message = $this->factory->createTextMessageWithAttachment();

        $encoded_attachment = file_get_contents($this->factory->getFixturePath("kitten.base64.txt"));

        $quoted_body = TestMessageFactory::TEXT_BODY_QUOTED_PRINTABLE;

        $expected_mime = <<<MIME
Date: Thu, 15 Sep 2016 17:20:54 +0200
To: =?UTF-8?Q?Rasmus =C3=A5h Schultz?= <blip@test.org>
From: blub@test.org, zoink@test.org
Sender: blub@test.org
Subject: Hey, Rasmus!
MIME-Version: 1.0
Content-Type: multipart/mixed; boundary="++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++"

This is a multipart message in MIME format.

--++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

{$quoted_body}
--++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++
Content-Type: image/jpeg
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename="kitten.jpg"

{$encoded_attachment}
--++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++--

MIME;

        $I->assertSame($expected_mime, $this->toMIME($message));
    }

    public function writeHTMLMessage(UnitTester $I)
    {
        $message = $this->factory->createHTMLMessage();

        $quoted_body = TestMessageFactory::HTML_BODY_QUOTED_PRINTABLE;

        $expected_mime = <<<MIME
Date: Thu, 15 Sep 2016 17:20:54 +0200
To: =?UTF-8?Q?Rasmus =C3=A5h Schultz?= <blip@test.org>
From: blub@test.org
Subject: Hey, Rasmus!
MIME-Version: 1.0
Content-Type: text/html; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

{$quoted_body}

MIME;

        $I->assertSame($expected_mime, $this->toMIME($message));
    }

    public function writeHTMLMessageWithAttachment(UnitTester $I)
    {
        $message = $this->factory->createHTMLMessageWithAttachment();

        $encoded_attachment = file_get_contents($this->factory->getFixturePath("kitten.base64.txt"));

        $quoted_body = TestMessageFactory::HTML_BODY_QUOTED_PRINTABLE;

        $expected_mime = <<<MIME
Date: Thu, 15 Sep 2016 17:20:54 +0200
To: =?UTF-8?Q?Rasmus =C3=A5h Schultz?= <blip@test.org>
From: blub@test.org
Subject: Hey, Rasmus!
MIME-Version: 1.0
Content-Type: multipart/mixed; boundary="++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++"

This is a multipart message in MIME format.

--++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++
Content-Type: text/html; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

{$quoted_body}
--++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++
Content-Type: image/jpeg
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename="kitten.jpg"

{$encoded_attachment}
--++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++--

MIME;

        $I->assertSame($expected_mime, $this->toMIME($message));
    }

    public function writeTextAndHTMLMessage(UnitTester $I)
    {
        $message = $this->factory->createTextAndHTMLMessage();

        $quoted_text = TestMessageFactory::TEXT_BODY_QUOTED_PRINTABLE;
        $quoted_html = TestMessageFactory::HTML_BODY_QUOTED_PRINTABLE;

        $expected_mime = <<<MIME
Date: Thu, 15 Sep 2016 17:20:54 +0200
To: Rasmus Schultz <blip@test.org>
From: blub@test.org
Subject: Hey, Rasmus!
MIME-Version: 1.0
Content-Type: multipart/alternative; boundary="++++alternative-aaafaf7c286e771e9bd71eacae9f26ae36c9c650++++"

--++++alternative-aaafaf7c286e771e9bd71eacae9f26ae36c9c650++++
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

{$quoted_text}
--++++alternative-aaafaf7c286e771e9bd71eacae9f26ae36c9c650++++
Content-Type: text/html; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

{$quoted_html}
--++++alternative-aaafaf7c286e771e9bd71eacae9f26ae36c9c650++++--

MIME;

        $I->assertSame($expected_mime, $this->toMIME($message));
    }

    public function writeTextAndHTMLMessageWithAttachment(UnitTester $I)
    {
        $message = $this->factory->createTextAndHTMLMessageWithAttachment();

        $encoded_attachment = file_get_contents($this->factory->getFixturePath("kitten.base64.txt"));

        $quoted_text_body = TestMessageFactory::TEXT_BODY_QUOTED_PRINTABLE;

        $quoted_html_body = TestMessageFactory::HTML_BODY_QUOTED_PRINTABLE;

        $expected_mime = <<<MIME
Date: Thu, 15 Sep 2016 17:20:54 +0200
To: =?UTF-8?Q?Rasmus =C3=A5h Schultz?= <blip@test.org>
From: blub@test.org
Subject: Hey, Rasmus!
MIME-Version: 1.0
Content-Type: multipart/mixed; boundary="++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++"

This is a multipart message in MIME format.

--++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++
Content-Type: multipart/alternative; boundary="++++alternative-5870793b3a929ca762d3e15521a8ff2b1e382f08++++"

--++++alternative-5870793b3a929ca762d3e15521a8ff2b1e382f08++++
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

${quoted_text_body}
--++++alternative-5870793b3a929ca762d3e15521a8ff2b1e382f08++++
Content-Type: text/html; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

{$quoted_html_body}
--++++alternative-5870793b3a929ca762d3e15521a8ff2b1e382f08++++--
--++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++
Content-Type: application/octet-stream
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename="kitten.jpg"

{$encoded_attachment}
--++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++--

MIME;

        $I->assertSame($expected_mime, $this->toMIME($message));
    }

    public function writeMessageWithMultipleAttachments(UnitTester $I)
    {
        // NOTE: this test also covers use of the Attachment::fromFile() factory method
        //       as well as constructing an Attachment instance using string content

        $message = $this->factory->createMessageWithMultipleAttachments();

        $encoded_first_attachment = file_get_contents($this->factory->getFixturePath("kitten.base64.txt"));

        $encoded_second_attachment = TestMessageFactory::TEXT_BODY_BASE64;

        $quoted_body = TestMessageFactory::TEXT_BODY_QUOTED_PRINTABLE;

        $expected_mime = <<<MIME
Date: Thu, 15 Sep 2016 17:20:54 +0200
To: Rasmus Schultz <blip@test.org>
From: blub@test.org
Subject: Hey, Rasmus!
MIME-Version: 1.0
Content-Type: multipart/mixed; boundary="++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++"

This is a multipart message in MIME format.

--++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

{$quoted_body}
--++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++
Content-Type: application/octet-stream
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename="kitten.jpg"

{$encoded_first_attachment}
--++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename="hello.txt"

{$encoded_second_attachment}
--++++mixed-03e3b4a304d78659f1a3c0a3cea31a4d73f3a426++++--

MIME;

        $I->assertSame($expected_mime, $this->toMIME($message));
    }

    public function writeMessageWithMultipleRecipients(UnitTester $I)
    {
        $message = $this->factory->createMessageWithMultipleRecipients();

        $expected_mime = <<<MIME
Date: Thu, 15 Sep 2016 17:20:54 +0200
To: Rasmus Schultz <blip@test.org>, Also Rasmus Schultz <also-blip@test.org>
From: blub@test.org
Subject: Hey, Rasmus!
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

Hello!

MIME;

        $I->assertSame($expected_mime, $this->toMIME($message));
    }

    public function writeMessageWithCCAndBCCRecipients(UnitTester $I)
    {
        $message = $this->factory->createMessageWithCCAndBCCRecipients();

        $expected_mime = <<<MIME
Date: Thu, 15 Sep 2016 17:20:54 +0200
To: blip@test.org
From: blub@test.org
Cc: joe@test.org
Bcc: bob@test.org
Subject: Hey, Rasmus!
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

Hello!

MIME;

        $I->assertSame($expected_mime, $this->toMIME($message));
    }

    public function writeMessageWithCustomHeaders(UnitTester $I)
    {
        $message = $this->factory->createMessageWithCustomHeaders();

        $expected_mime = <<<MIME
Date: Thu, 15 Sep 2016 17:20:54 +0200
To: blip@test.org
From: blub@test.org
Subject: Hey, Rasmus!
MIME-Version: 1.0
X-Custom-Header: custom-value
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: quoted-printable

Hello!

MIME;

        $I->assertSame($expected_mime, $this->toMIME($message));
    }

    /**
     * @param Message $message
     *
     * @return string
     */
    private function toMIME(Message $message)
    {
        $temp = fopen("php://temp", "rw+");

        $writer = new MockMIMEWriter($temp);

        $writer->writeMessage($message);

        rewind($temp);

        $mime = stream_get_contents($temp);

        fclose($temp);

        $this->last_mime = $mime;

        return $mime;
    }
}
