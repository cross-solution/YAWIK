Feature: Organization profile
    In order to apply a job
    As an applicant
    I should able to view organization profile

    Background:
        Given I am logged in as a recruiter with "Cross Solution" as organization
        And organization "Cross Solution" have jobs:
            | title             | professions | industries      | employment types | status    |
            | Software Engineer | IT          | IT & Internet   | Contract         | published |
            | Tester            | IT          | IT & Internet   | Permanent        | published |
            | Senior Developer  | IT          | IT & Internet   | Permanent        | published |
            | Draft Engineer    | IT          | IT & Internet   | Contract         | draft     |
        And I define contact for "Cross Solution" organization with:
            | Street        | Some Street   |
            | House Number  | 1734          |
            | Postal Code   | 7536          |
            | City          | Frankfurt     |
            | Country       | Germany       |
            | Phone         | 4121345       |
            | Fax           | 4121345       |
        And I have organization "Profile Active Job"
        And profile setting for "Profile Active Job" is "active-jobs"
        And I have organization "Profile Disabled"
        And profile setting for "Profile Disabled" is "disabled"
        And organization "Profile Disabled" have jobs:
            | title             | professions | industries      | employment types | status    |
            | Mentor            | IT          | IT & Internet   | Contract         | published |

    @organization @profile
    Scenario: Browse Organization Profiles
        Given I want to see list organization profiles
        Then I should see "Organizations Profile"
        And the "#cam-organization-list" element should contain "Cross Solution"
        And I should not see "Profile Active Job"
        And I should not see "Profile Disabled"

    @organization @profile
    Scenario: Profile only visible when active jobs available
        Given organization "Profile Active Job" have jobs:
            | title             | professions | industries      | employment types | status    |
            | Cooker            | IT          | IT & Internet   | Contract         | published |
        And I want to see list organization profiles
        Then I should see "Profile Active Job"

    @organization @profile
    Scenario: Profile Detail Page For an Organization
        Given I go to profile page for organization "Cross Solution"
        Then I should see "Cross Solution"
        And I should see "Some Street 1734"
        And I should see "Frankfurt 7536"
        And I should see "Phone: 4121345"
        And I should see "Fax: 4121345"
        And I should see "Software Engineer"
        And I should see "Tester"
        And I should see "Senior Developer"

    @organization @profile
    Scenario: Access profile with invalid id
        Given I go to "/en/organizations/profile/invalidid"
        Then I should see "Entity with id \"invalidid\" not found"

    @organization @profile
    Scenario: Access disabled organization profile
        Given I go to profile page for organization "Profile Disabled"
        Then I should see "This Organization Profile is disabled"

    @organization @profile
    Scenario: Access profile when there are no active jobs
        Given organization "Profile Active Job" have no job
        And I go to profile page for organization "Profile Active Jobs"
        Then I should see "This Organization Profile is disabled"
