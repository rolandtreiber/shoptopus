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
                sh 'docker compose -f docker-compose-ci.yml ps'
            }
        }
        stage("Run Composer Install") {
            steps {
                sh 'docker compose -f docker-compose-ci.yml run --rm sh-composer install --ignore-platform-reqs --no-interaction'
            }
        }
        stage("Run Tests") {
            steps {
                sh 'docker compose -f docker-compose-ci.yml run --rm sh-artisan test'
            }
        }
        stage("Delete .env file") {
            steps {
                sh 'rm ./.env'
            }
        }
        stage("Create artifact") {
            steps {
                zip zipFile: 'shoptopus.zip', archive: true, overwrite: true, exclude: 'elasticsearch_data/, public/uploads/'
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
        stage("Unzip artifact in place") {
            steps {
                sh 'unzip -o /Users/rolandtreiber/Sites/shoptopus.zip -d /Users/rolandtreiber/Sites/shoptopus'
            }
        }
        stage("Delete artifact zip file") {
            steps {
                sh 'rm /Users/rolandtreiber/Sites/shoptopus.zip'
            }
        }
        stage("Running the migrations") {
            steps {
                sh '/usr/local/bin/php /Users/rolandtreiber/Sites/shoptopus/artisan migrate'
            }
        }
        stage("Restart supervisor") {
            steps {
                sh 'supervisorctl restart all'
            }
        }
    }
    post {
        always {
            sh 'docker compose -f docker-compose-ci.yml down --remove-orphans -v'
            sh 'docker compose -f docker-compose-ci.yml ps'
        }
    }
}
