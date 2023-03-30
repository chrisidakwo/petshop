<?php

namespace Tests\Unit\Auth\Validators;

use App\Auth\Contracts\Validator;
use App\Auth\Validators\TokenValidator;
use App\Exceptions\InvalidBearerToken;
use Tests\TestCase;

class TokenValidatorTest extends TestCase
{
    protected Validator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new TokenValidator();
    }

    public function testItShouldThrowExceptionOnAMalformedToken(): void
    {
        $this->expectException(InvalidBearerToken::class);
        $this->expectExceptionMessage('Wrong number of segments');

        $this->validator->validate('is_an_valid.token');
    }

    public function testItShouldReturnTrueOnWellFormedToken(): void
    {
        $this->assertTrue($this->validator->isValid('is_a.valid.token'));
    }

    public function testItShouldReturnFalseOnAMalformedToken(): void
    {
        $this->assertFalse($this->validator->isValid('is_an_valid.token'));
    }

    public function testItValidatesOnAWellFormedToken(): void
    {
        $this->assertTrue(
            $this->validator->isValid(
                'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJqdGkiOiJSQmZxcWdzQjgxVGwiLCJzdWIiOiIxIiwiaXNzIjoiL2xvcmVtLXVybCIsImlhdCI6MTY4MDE2MDM2NiwibmJmIjoxNjgwMTYwMzY2LCJleHAiOjE2ODAxNjM5NjYsImN1c3RvbSI6ImNocmlzLmlkYWt3b0BnbWFpbC5jb20ifQ.BGqHNWMjLvzzI4RfhkBqJrIiwwnYRVW6i20HVljdD8Hvv6kvHoji5KUiqKxP6p9g5vQQgn3RXVDl9n2xl8tfODf1BCjwqEidAaxLv4DIwUe7rJ4k8yUPUYzXTZM1WeEAR5BkTK6DEGuvyA-zHaDBOwLa10thcFet-sZ9s6lcif9on_Mry_eOGwibeRJY3kx-Yj8Lvi7cKIqHEeBCTOr51uYgnJ-iLaj6Am03KPcmn_bo5ZHuVm2bXaxI9gAOmikahOCIXWUo0zlvtx1Eqpb3F6Lm_trqrnscdm3wx8GWg87qa_5NcCltUi6_sCoZ6DuCAooU88suxyaYsb9lfsWHDw',
            ),
        );
    }

    public function testItReturnsAWellFormedAndValidatedToken()
    {
        $this->assertNotEmpty(
            $this->validator->validate(
                'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJqdGkiOiIyZHNYUXBiUjlRTUwiLCJzdWIiOiIxIiwiaXNzIjoiL2xvcmVtLXVybCIsImlhdCI6MTY4MDE2MDUxMSwibmJmIjoxNjgwMTYwNTExLCJleHAiOjE2ODAxNjQxMTEsImN1c3RvbSI6ImNocmlzLmlkYWt3b0BnbWFpbC5jb20ifQ.FHVCo8QqRMIHWO4JdNBMruVCNNXZ7CTdKbxov0ILX4Rim0Cj-ztWzTMJMjTNGHLvcBHQEfs-5OUwpDET7joboWTlQMGgRJo_tLdZHIqx1tOb0wDl1GzNDBH-VCgbY2AyEiuO2HShIg5Gw0QGzLjAY1zV5VCd_w8jrrEL-uFeRYD5cciy_6yQLuUGdhb1uGtFrrZPy9A77YiEyvFqYCn-KtAZc7dZ87TV3h-FLBlx4Ppb69w9ZAOF7xW01QwvHR4ZMHAd03INF4VqRHByHpTX3pgN-QqA8dCugr0pPfS6TPwEdhqwVukbOrmht5eQF8v8NP6_-AigODqaIAM94ddLHQ',
            ),
        );
    }
}
