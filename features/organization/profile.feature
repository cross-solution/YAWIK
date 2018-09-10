@organization
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
            | Database Engineer | IT          | IT & Internet   | Permanent        | published |
            | System Analyst    | IT          | IT & Internet   | Permanent        | published |
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

    Scenario: Browse Organization Profiles
        Given I want to see list organization profiles
        Then I should see "Organizations Profile"
        And the "#cam-organization-list" element should contain "Cross Solution"
        And I should not see "Profile Active Job"
        And I should not see "Profile Disabled"

    Scenario: Profile only visible when active jobs available
        Given organization "Profile Active Job" have jobs:
            | title             | professions | industries      | employment types | status    |
            | Cooker            | IT          | IT & Internet   | Contract         | published |
        And I want to see list organization profiles
        Then I should see "Profile Active Job"

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

    Scenario: Access profile with invalid id
        Given I go to "/en/organizations/profile/invalidid"
        Then I should see "Entity with id \"invalidid\" not found"

    Scenario: Access disabled organization profile
        Given I go to profile page for organization "Profile Disabled"
        Then I should see "This profile is disabled."

    Scenario: Access profile when there are no active jobs
        Given organization "Profile Active Job" have no job
        And I go to profile page for organization "Profile Active Jobs"
        Then I should see "This profile is disabled."

    Scenario: Filter organization profile by user roles
        Given I have a recruiter with the following:
        | Full Name        | Second Recruiter        |
        | login            | recruiter@example.com   |
        | organization     | Recruiter Organization  |
        And organization "Recruiter Organization" have jobs:
        | title             | professions | industries      | employment types | status    |
        | Job Recruiter     | IT          | IT & Internet   | Contract         | published |
        And I am logged out
        # view profile as guest
        When I want to see list organization profiles
        Then I should see "Cross Solution"
        And I should see "Recruiter Organization"
        # view profile as recruiter
        When I am logged in as "recruiter@example.com" identified by "test"
        And I want to see list organization profiles
        Then I should see "Recruiter Organization"
        And I should not see "Cross Solution"
        And I should not see "Profile Active Job"
        When I go to profile page for organization "Recruiter Organization"
        Then I should see "Job Recruiter"
