<?php


namespace Tests\AppBundle\Security;

use AppBundle\Entity\AdventureList;
use AppBundle\Entity\User;
use AppBundle\Security\AdventureListVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AdventureListVoterTest extends VoterTest
{
    const TOKEN_SECRET = '123';
    const LIST_ATTRIBUTE = 'list';
    const CREATE_ATTRIBUTE = 'create';

    /**
     * @var AdventureListVoter
     */
    private $voter;

    public function setUp()
    {
        $this->voter = new AdventureListVoter();
    }

    /**
     * @dataProvider unsupportedSubjectsAndAttributesDataProvider
     */
    public function testUnsupportedSubjectsAndAttributes($subject, $attribute)
    {
        $result = $this->voter->vote($this->createAnonymousToken(), $subject,
            [$attribute]);
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function unsupportedSubjectsAndAttributesDataProvider()
    {
        return [
            ['a subject', 'delete'],
            ['a subject', 'create'],
            ['adventure_list', 'something'],
            [new AdventureList(), 'something'],
            [new AdventureList(), self::LIST_ATTRIBUTE],
        ];
    }

    /**
     * @dataProvider listAndCreateAttributeDataProvider
     */
    public function testListAndCreateAttribute($attribute, $token, $subject, $expectedResult)
    {
        $result = $this->voter->vote($token, $subject, [$attribute]);
        $this->assertSame($expectedResult, $result);
    }

    public function listAndCreateAttributeDataProvider()
    {
        return [
            [
                self::LIST_ATTRIBUTE,
                $this->createAnonymousToken(),
                'adventure_list',
                VoterInterface::ACCESS_DENIED,
            ],
            [
                self::LIST_ATTRIBUTE,
                $this->createUserToken(),
                'adventure_list',
                VoterInterface::ACCESS_GRANTED,
            ],
            [
                self::CREATE_ATTRIBUTE,
                $this->createAnonymousToken(),
                new AdventureList(),
                VoterInterface::ACCESS_DENIED,
            ],
            [
                self::CREATE_ATTRIBUTE,
                $this->createUserToken(),
                new AdventureList(),
                VoterInterface::ACCESS_GRANTED,
            ],
        ];
    }

    /**
     * @dataProvider otherAttributesDataProvider
     */
    public function testOtherAttributes($token, $subject, $expectedResult)
    {
        foreach (['view', 'edit', 'delete'] as $attribute) {
            $result = $this->voter->vote($token, $subject, [$attribute]);
            $this->assertSame($expectedResult, $result);
        }
    }

    public function otherAttributesDataProvider()
    {
        $myself = $this->createUser(1, ['ROLE_USER']);
        $myAdventureList = $this->createMock(AdventureList::class);
        $myAdventureList->method('getUser')->willReturn($myself);

        $you = $this->createUser(2, ['ROLE_USER']);
        $yourAdventureList = $this->createMock(AdventureList::class);
        $yourAdventureList->method('getUser')->willReturn($you);

        return [
            [
                $this->createAnonymousToken(),
                new AdventureList(),
                VoterInterface::ACCESS_DENIED,
            ],
            [
                $this->createUserToken($myself),
                $myAdventureList,
                VoterInterface::ACCESS_GRANTED,
            ],
            [
                $this->createUserToken($myself),
                $yourAdventureList,
                VoterInterface::ACCESS_DENIED,
            ],
        ];
    }
}
