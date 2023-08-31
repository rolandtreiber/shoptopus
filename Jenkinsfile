pipeline {
    agent any
    stages {
        stage("Verify tooling") {
            steps {
                sh '''
                    docker info
                    docker version
                    docker compose version
                '''
            }
        }
        stage("Clear all running docker containers") {
            steps {
                script {
                    try {
                        sh 'docker rm -f $(docker ps -a -q)'
                    } catch (Exception e) {
                        echo 'No running container to clear up...'
                    }
                }
            }
        }
        stage("Copy .env file") {
            steps {
                sh 'cp ./.env.example ./.env'
            }
        }
        stage("Delete .env file") {
            steps {
                sh 'rm ./.env'
            }
        }        
        stage("Copy artifact") {
            steps {
                fileOperations([fileCopyOperation(
                excludes: '',
                flattenFiles: false,
                includes: 'shoptopus.zip',
                targetLocation: "/Users/rolandtreiber/Sites"
                )])
            }
        }
        stage("Delete .env file") {
            steps {
                sh 'unzip -o /Users/rolandtreiber/Sites/shoptopus.zip'
                sh 'rm /Users/rolandtreiber/Sites/shoptopus.zip'
            }
        }        
    }
    post {
        always {
            sh 'docker compose down --remove-orphans -v'
            sh 'docker compose ps'
        }
    }
}
