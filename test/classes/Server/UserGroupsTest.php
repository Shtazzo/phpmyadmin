<?php
/**
 * Tests for PhpMyAdmin\Server\UserGroups
 *
 * @package PhpMyAdmin-test
 */
declare(strict_types=1);

namespace PhpMyAdmin\Tests\Server;

use PhpMyAdmin\Server\UserGroups;
use PhpMyAdmin\Url;
use PHPUnit\Framework\TestCase;

/**
 * Tests for PhpMyAdmin\Server\UserGroups
 *
 * @package PhpMyAdmin-test
 */
class UserGroupsTest extends TestCase
{
    /**
     * Prepares environment for the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $GLOBALS['cfg']['ServerDefault'] = 1;
        $GLOBALS['cfg']['ActionLinksMode'] = 'both';

        $GLOBALS['server'] = 1;
        $_SESSION['relation'][$GLOBALS['server']] = [
            'PMA_VERSION' => PMA_VERSION,
            'db' => 'pmadb',
            'users' => 'users',
            'usergroups' => 'usergroups',
        ];
    }

    /**
     * Tests UserGroups::getHtmlForUserGroupsTable() function when there are no user groups
     *
     * @return void
     *
     * @group medium
     */
    public function testGetHtmlForUserGroupsTableWithNoUserGroups()
    {
        $expectedQuery = 'SELECT * FROM `pmadb`.`usergroups`'
            . ' ORDER BY `usergroup` ASC';

        $dbi = $this->getMockBuilder('PhpMyAdmin\DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $dbi->expects($this->once())
            ->method('tryQuery')
            ->with($expectedQuery)
            ->will($this->returnValue(true));
        $dbi->expects($this->once())
            ->method('numRows')
            ->withAnyParameters()
            ->will($this->returnValue(0));
        $dbi->expects($this->once())
            ->method('freeResult');
        $GLOBALS['dbi'] = $dbi;

        $html = UserGroups::getHtmlForUserGroupsTable();
        $this->assertStringNotContainsString(
            '<table id="userGroupsTable">',
            $html
        );
        $url_tag = '<a href="' . Url::getFromRoute('/server/user_groups', ['addUserGroup' => 1]);
        $this->assertStringContainsString(
            $url_tag,
            $html
        );
    }

    /**
     * Tests UserGroups::getHtmlForUserGroupsTable() function when there are user groups
     *
     * @return void
     */
    public function testGetHtmlForUserGroupsTableWithUserGroups()
    {
        $expectedQuery = 'SELECT * FROM `pmadb`.`usergroups`'
            . ' ORDER BY `usergroup` ASC';

        $dbi = $this->getMockBuilder('PhpMyAdmin\DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $dbi->expects($this->once())
            ->method('tryQuery')
            ->with($expectedQuery)
            ->will($this->returnValue(true));
        $dbi->expects($this->once())
            ->method('numRows')
            ->withAnyParameters()
            ->will($this->returnValue(1));
        $dbi->expects($this->at(2))
            ->method('fetchAssoc')
            ->withAnyParameters()
            ->will(
                $this->returnValue(
                    [
                        'usergroup' => 'usergroup',
                        'tab' => 'server_sql',
                        'allowed' => 'Y',
                    ]
                )
            );
        $dbi->expects($this->at(3))
            ->method('fetchAssoc')
            ->withAnyParameters()
            ->will($this->returnValue(false));
        $dbi->expects($this->once())
            ->method('freeResult');
        $GLOBALS['dbi'] = $dbi;

        $html = UserGroups::getHtmlForUserGroupsTable();
        $this->assertStringContainsString(
            '<td>usergroup</td>',
            $html
        );
        $url_tag = '<a class="" href="' . Url::getFromRoute('/server/user_groups') . '" data-post="'
            . Url::getCommon(
                [
                    'viewUsers' => 1,
                    'userGroup' => htmlspecialchars('usergroup'),
                ],
                ''
            );
        $this->assertStringContainsString(
            $url_tag,
            $html
        );
        $url_tag = '<a class="" href="' . Url::getFromRoute('/server/user_groups') . '" data-post="'
            . Url::getCommon(
                [
                    'editUserGroup' => 1,
                    'userGroup' => htmlspecialchars('usergroup'),
                ],
                ''
            );
        $this->assertStringContainsString(
            $url_tag,
            $html
        );
        $url_tag = '<a class="deleteUserGroup ajax" href="' . Url::getFromRoute('/server/user_groups') . '" data-post="'
            . Url::getCommon(
                [
                    'deleteUserGroup' => 1,
                    'userGroup' => htmlspecialchars('usergroup'),
                ],
                ''
            );
        $this->assertStringContainsString(
            $url_tag,
            $html
        );
    }

    /**
     * Tests UserGroups::delete() function
     *
     * @return void
     */
    public function testDeleteUserGroup()
    {
        $userDelQuery = 'DELETE FROM `pmadb`.`users`'
            . " WHERE `usergroup`='ug'";
        $userGrpDelQuery = 'DELETE FROM `pmadb`.`usergroups`'
            . " WHERE `usergroup`='ug'";

        $dbi = $this->getMockBuilder('PhpMyAdmin\DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $dbi->expects($this->at(1))
            ->method('query')
            ->with($userDelQuery);
        $dbi->expects($this->at(3))
            ->method('query')
            ->with($userGrpDelQuery);
        $dbi->expects($this->any())
            ->method('escapeString')
            ->will($this->returnArgument(0));

        $GLOBALS['dbi'] = $dbi;

        UserGroups::delete('ug');
    }

    /**
     * Tests UserGroups::getHtmlToEditUserGroup() function
     *
     * @return void
     */
    public function testGetHtmlToEditUserGroup()
    {
        // adding a user group
        $html = UserGroups::getHtmlToEditUserGroup();
        $this->assertStringContainsString(
            '<input type="hidden" name="addUserGroupSubmit" value="1"',
            $html
        );
        $this->assertStringContainsString(
            '<input type="text" name="userGroup"',
            $html
        );

        $expectedQuery = 'SELECT * FROM `pmadb`.`usergroups`'
            . " WHERE `usergroup`='ug'";
        $dbi = $this->getMockBuilder('PhpMyAdmin\DatabaseInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $dbi->expects($this->once())
            ->method('tryQuery')
            ->with($expectedQuery)
            ->will($this->returnValue(true));
        $dbi->expects($this->exactly(2))
            ->method('fetchAssoc')
            ->willReturnOnConsecutiveCalls(
                [
                    'usergroup' => 'ug',
                    'tab' => 'server_sql',
                    'allowed' => 'Y',
                ],
                false
            );
        $dbi->expects($this->once())
            ->method('freeResult');
        $dbi->expects($this->any())
            ->method('escapeString')
            ->will($this->returnArgument(0));

        $GLOBALS['dbi'] = $dbi;

        // editing a user group
        $html = UserGroups::getHtmlToEditUserGroup('ug');
        $this->assertStringContainsString(
            '<input type="hidden" name="userGroup" value="ug"',
            $html
        );
        $this->assertStringContainsString(
            '<input type="hidden" name="editUserGroupSubmit" value="1"',
            $html
        );
        $this->assertStringContainsString(
            '<input type="hidden" name="editUserGroupSubmit" value="1"',
            $html
        );
        $this->assertStringContainsString(
            '<input type="checkbox" class="checkall" checked="checked"'
            . ' name="server_sql" value="Y">',
            $html
        );
        $this->assertStringContainsString(
            '<input type="checkbox" class="checkall"'
            . ' name="server_databases" value="Y">',
            $html
        );
    }
}
