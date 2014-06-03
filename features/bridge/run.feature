Feature: Developer change some file
    As a Developer
    I want to run behat command
    In order to get feedback on a state of my application

    Background:
        Given the config file contains:
              """
              behat:
                  watch:
                      - { pattern: "#^features/(.+)\.feature#" }
              """

    Scenario: Running successfully behat with phpguard patch
        Given I run phpguard with "start -vvv"
          And I create file "features/some.feature" with contents:
              """
              Feature: Some Feature
                As something
                I want something
                In order to get something

                Scenario: Some Scenario
                    Given I have passed step
                     Then I have failed step
              """
         Then I should see "1 scenario"
          And I should see "2 steps"
          And I should see "1 passed"
          And I should see "1 failed"

    Scenario: Running successfully behat with phpguard patch
        Given the file "features/some.feature" contains:
              """
              Feature: Some Feature
                As something
                I want something
                In order to get something

                Scenario: Some Scenario
                    Given I have passed step
                     Then I have failed step
              """
         When I run behat
         Then I should see "1 scenario"
          And I should see "2 steps"
          And I should see "1 passed"
          And I should see "1 failed"