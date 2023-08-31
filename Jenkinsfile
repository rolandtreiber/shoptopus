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
        stage("Start Docker") {
            steps {
                sh 'make up'
                sh 'docker compose ps'
            }
        }
        stage("Create artifact") {
            steps {
                zip zipFile: 'artifact.zip', archive: true, overwrite: true, exclude: 'elasticsearch_data/'
            }
        }
        stage("Copy artifact") {
            steps {
                fileOperations([fileCopyOperation(
                excludes: '',
                flattenFiles: false,
                includes: 'artifact.zip',
                targetLocation: "/Users/rolandtreiber/Sites/shoptopus"
                )])
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
