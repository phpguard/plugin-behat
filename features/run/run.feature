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

    Scenario: Running successfully phpguard
        Given I run phpguard with "start -vvv" arguments
         Then I should see "Plugin Behat activated"
          And I should see file "features/bootstrap/FeatureContext.php"
         When I create file "features/some.feature" with contents:
              """
              Feature: Some Feature
                As something
                I want something
                In order to get something

                Scenario: Some Scenario
                    Given I have pass step
                     Then I have failed step
              """
         Then I should see file "features/some.feature"
          And I should see "features/some.feature"
          And I should see "1 scenario"
          And I should see "2 steps"
          And I should see "1 passed"
          And I should see "1 failed"