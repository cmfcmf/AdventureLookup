<?php


namespace Tests\AppBundle\Security;

use AppBundle\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

abstract class VoterTest extends TestCase
{
    const TOKEN_SECRET = '123';

    /**
     * @return MockObject|AccessDecisionManagerInterface
     */
    protected function createAccessDecisionManagerMock()
    {
        // This matches the hierarchy defined in security.yml.
        $roleHierarchy = new RoleHierarchy([
            "ROLE_CURATOR" => ["ROLE_USER"],
            "ROLE_ADMIN" => ["ROLE_CURATOR", "ROLE_ALLOWED_TO_SWITCH"]
        ]);
        $userVoter = new RoleHierarchyVoter($roleHierarchy);
        return new AccessDecisionManager([$userVoter]);
    }

    /**
     * @param $userId
     * @param $roles
     * @return User|MockObject
     */
    protected function createUser($userId, $roles)
    {
        $myself = $this->createMock(User::class);
        $myself->method('getId')->willReturn($userId);
        $myself->method('getUsername')->willReturn("user " . $userId);
        $myself->method('getRoles')->willReturn($roles);

        return $myself;
    }

    protected function createAnonymousToken(): TokenInterface
    {
        return new AnonymousToken(self::TOKEN_SECRET, 'anon.', []);
    }

    protected function createUserToken(User $user = null): TokenInterface
    {
        if ($user === null) {
            $user = new User();
        }
        return new UsernamePasswordToken($user, [], 'user_provider', $user->getRoles());
    }
}
