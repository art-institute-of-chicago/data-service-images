## Requirements

This project requires the following Python packages:

1. [Pillow](https://python-pillow.org/)

If you are just getting started with Python, before you do anything, I recommend installing the following programs, for workflow reasons:

1. [pyenv](https://github.com/pyenv/pyenv) - Python version manager
2. [virtualenv](https://virtualenv.pypa.io/en/stable/) - Python sandboxer

These will work together with [pip](https://pip.pypa.io/en/stable/), which is a package manager that comes with most Python installations, to ease the process of managing package and version dependencies on a per-project basis.


## Installation

Because we don't typically work with Python on our team, these instructions assume that you are setting up a Python dev environment from scratch. We'll walk through the steps to get started, with the aim of reducing potential friction down the line.

We assume that you are working on macOS, which comes with Python already installed. You may want to avoid using that Python, and install a Python version manager instead. We use [pyenv](https://github.com/pyenv/pyenv) for this purpose, and the project includes a `.python-version` file.

Don't worry, pyenv will allow you to switch to your system-installed Python version at any time, so if you've already got a bunch of packages installed globally (:grimacing:), you should be able to use them as normal.

Next, we will install [virtualenv](https://virtualenv.pypa.io/en/stable/). Most Python installations come with the [pip](https://pip.pypa.io/en/stable/) package manager. It does the job fine, but it sucks at managing dependencies on a per-project basis. Whenever you install a package with pip, you are installing it globally for that Python version. This can cause all sort of cross-project conflicts.

Virtualenv aims to solve this problem by allowing you to create a sandboxed environment for each Python project. When you activate an environment, any package installations will be directed to that project's sandbox. No more
version conflicts.

### Pyenv

Install [pyenv](https://github.com/pyenv/pyenv):

```
brew update
brew install pyenv
```

Add this to your `.bashrc`:

```bash
# To use Homebrew's directories rather than $HOME/.pyenv, use:
# export PYENV_ROOT="/usr/local/var/pyenv"

# Don't set PYENV_ROOT="~/.pyenv", macOS doesn't like the tilde

export PYENV_ROOT="$HOME/.pyenv"
export PYENV_SHELL="bash"

# To enable shims and autocompletion
if which pyenv > /dev/null; then eval "$(pyenv init -)"; fi
```

Pyenv works by prepending `$PYENV_ROOT/shims` to your `$PATH`, so that requests to `python` etc. resolve in the shims directory, instead of with the system-installed Python.

As such, please ensure these commands are called _after_ any other `.bashrc` lines that may result in `/usr/bin` being placed before `PYENV_ROOT/shims` in your `$PATH`.

Ensure the changes take effect. I recommend not running `source ~/.bashrc` in this case, since there's a chance that the pyenv install process has temporarily polluted your `$PATH`. Just open a new terminal window instead to start from a clean slate.

Install and activate Python 3.7 using pyenv:

```bash
pyenv global # should be `system`
pyenv install 3.7
pyenv versions # should list `3.7`
pyenv global 3.7
which python # should be e.g. /Users/foobar/.pyenv/shims/python
```

### Virtualenv

Install [virtualenv](https://virtualenv.pypa.io/en/stable/):

```bash
pip install virtualenv
```

Create and activate a virtual environment for the project:

```bash
cd ~/path/to/data-service-images
pyenv versions # should be 3.7 from .python-version
virtualenv env
source env/bin/activate # macOS
source env/Scripts/activate # Windows w/ git-bash
```

You will need to run `source env/bin/activate` each time you work with this project. You can exit the environment by running `deactivate`. Consult the virtualenv [User Guide](https://virtualenv.pypa.io/en/stable/userguide/) for more info.

Finally, install the project's package requirements:

```bash
pip -r requirements.txt
```

You should be ready to use the tool.
