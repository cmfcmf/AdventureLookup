<?php


namespace Tests\AppBundle\Security;

use AppBundle\Entity\Adventure;
use AppBundle\Security\AdventureVoter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AdventureVoterTest extends VoterTest
{
    /**
     * @var AdventureVoter
     */
    private $voter;

    public function setUp()
    {
        $this->voter = new AdventureVoter($this->createAccessDecisionManagerMock());
    }

    /**
     * @dataProvider unsupportedSubjectsAndAttributesDataProvider
     */
    public function testUnsupportedSubjectsAndAttributes($subject, $attribute)
    {
        $result = $this->voter->vote($this->createAnonymousToken(), $subject, [$attribute]);
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function unsupportedSubjectsAndAttributesDataProvider()
    {
        return [
            ['a subject', AdventureVoter::VIEW],
            ['a subject', AdventureVoter::CREATE],
            ['a subject', AdventureVoter::EDIT],
            ['a subject', AdventureVoter::DELETE],
            ['adventure', AdventureVoter::VIEW],
            [new Adventure(), 'something'],
        ];
    }

    /**
     * @dataProvider viewAndCreateAttributeDataProvider
     */
    public function testViewAndCreateAttribute($attribute, $token, $subject, $expectedResult)
    {
        $result = $this->voter->vote($token, $subject, [$attribute]);
        $this->assertSame($expectedResult, $result);
    }

    public function viewAndCreateAttributeDataProvider()
    {
        return [
            [
                AdventureVoter::CREATE,
                $this->createAnonymousToken(),
                new Adventure(),
                VoterInterface::ACCESS_DENIED,
            ],
            [
                AdventureVoter::CREATE,
                $this->createUserToken(),
                new Adventure(),
                VoterInterface::ACCESS_GRANTED
            ],
            [
                AdventureVoter::VIEW,
                $this->createAnonymousToken(),
                new Adventure(),
                VoterInterface::ACCESS_GRANTED,
            ],
            [
                AdventureVoter::VIEW,
                $this->createUserToken(),
                new Adventure(),
                VoterInterface::ACCESS_GRANTED
            ],
        ];
    }

    /**
     * @dataProvider editAndDeleteAttributeDataProvider
     */
    public function testEditAndDeleteAttribute($token, $subject, $attribute, $expectedResult)
    {
        $result = $this->voter->vote($token, $subject, [$attribute]);
        $this->assertSame($expectedResult, $result);
    }

    public function editAndDeleteAttributeDataProvider()
    {
        $myself = $this->createUser(1, ["ROLE_USER"]);
        $myAdventure = $this->createMock(Adventure::class);
        $myAdventure->method('getCreatedBy')->willReturn($myself->getUsername());

        $you = $this->createUser(2, ["ROLE_USER"]);
        $yourAdventure = $this->createMock(Adventure::class);
        $yourAdventure->method('getCreatedBy')->willReturn($you->getUsername());

        $curator = $this->createUser(3, ["ROLE_CURATOR"]);
        $admin = $this->createUser(4, ["ROLE_ADMIN"]);

        return [
            // edit
            [
                $this->createAnonymousToken(),
                new Adventure(),
                AdventureVoter::EDIT,
                VoterInterface::ACCESS_DENIED,
            ],
            [
                $this->createUserToken($myself),
                $myAdventure,
                AdventureVoter::EDIT,
                VoterInterface::ACCESS_GRANTED,
            ],
            [
                $this->createUserToken($myself),
                $yourAdventure,
                AdventureVoter::EDIT,
                VoterInterface::ACCESS_DENIED,
            ],
            [
                $this->createUserToken($curator),
                $myAdventure,
                AdventureVoter::EDIT,
                VoterInterface::ACCESS_GRANTED,
            ],
            [
                $this->createUserToken($admin),
                $myAdventure,
                AdventureVoter::EDIT,
                VoterInterface::ACCESS_GRANTED,
            ],
            // delete
            [
                $this->createAnonymousToken(),
                new Adventure(),
                AdventureVoter::DELETE,
                VoterInterface::ACCESS_DENIED,
            ],
            [
                $this->createUserToken($myself),
                $myAdventure,
                AdventureVoter::DELETE,
                VoterInterface::ACCESS_GRANTED,
            ],
            [
                $this->createUserToken($myself),
                $yourAdventure,
                AdventureVoter::DELETE,
                VoterInterface::ACCESS_DENIED,
            ],
            [
                $this->createUserToken($curator),
                $myAdventure,
                AdventureVoter::DELETE,
                VoterInterface::ACCESS_DENIED,
            ],
            [
                $this->createUserToken($admin),
                $myAdventure,
                AdventureVoter::DELETE,
                VoterInterface::ACCESS_GRANTED,
            ],
        ];
    }
}
