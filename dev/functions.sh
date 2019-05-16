#!/usr/bin/env bash

git_clone_required_plugins(){
	plugins_folder="${WP_ROOT_FOLDER}/wp-content/plugins"

	cd ${plugins_folder}

	declare -a required_plugins=(`echo ${REQUIRED_PLUGIN_REPOS}`);

	for plugin_repo in "${required_plugins[@]}"; do
		plugin_repo_url="git://github.com/${plugin_repo}.git"
		plugin_slug="$(basename ${plugin_repo})"

	  	if [[ -n "$(git ls-remote --heads ${plugin_repo_url} ${TRAVIS_PULL_REQUEST_BRANCH})" ]]; then
			branch="${TRAVIS_PULL_REQUEST_BRANCH}";
	  	elif [[ -n "$(git ls-remote --heads ${plugin_repo_url} ${TRAVIS_BRANCH})" ]]; then
			branch="${TRAVIS_BRANCH}";
	  	else
			branch="master";
	  	fi;

		echo "Cloning branch ${branch} for plugin ${plugin_slug}";

	  	git clone --single-branch --branch ${branch} ${plugin_repo_url} ${plugin_slug};

		cd ${plugin_slug};

		# Tweak git to correctly work with submodules.
	 	sed -i 's/git@github.com:/git:\/\/github.com\//' .gitmodules

	  	git submodule update --recursive --init;

	  	composer update --prefer-dist;

		if [[ $plugin_slug == "the-events-calendar" ]]; then
	  		cd common;

			composer update --prefer-dist;
		fi;

	  	cd ${plugins_folder}
	done
}