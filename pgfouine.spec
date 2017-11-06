Summary:	PgFouine PostgreSQL log analyzer
Name:		pgfouine
Version:	1.2
Release:	1%{?dist}
BuildArch:	noarch
License:	GPLv2+
Group:		Development/Tools
Source0:	http://pgfouine.projects.postgresql.org/releases/%{name}-%{version}.tar.gz
Source2:	pgfouine-tutorial.txt
URL: 		http://pgfouine.projects.postgresql.org/
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)

Patch1:		pgfouine-0.7-include_path.patch

%description
pgFouine is a PostgreSQL log analyzer. It generates text 
or HTML reports from PostgreSQL log files. These reports 
contain the list of the slowest queries, the queries that 
take the most time and so on.

pgFouine can also:
- analyze VACUUM VERBOSE output to help you improve your 
VACUUM strategy,
- generate Tsung sessions file to benchmark your 
PostgreSQL server.

%prep
%setup -q 
%patch1 -p0
sed -i 's!@INCLUDEPATH@!%{_datadir}/%{name}!' pgfouine_vacuum.php
sed -i 's!@INCLUDEPATH@!%{_datadir}/%{name}!' pgfouine.php

cp %{SOURCE2} .

%build

%install
# cleaning build environment
rm -rf %{buildroot}

# creating required directories
install -m 755 -d %{buildroot}/%{_datadir}/%{name}
install -m 755 -d %{buildroot}/%{_bindir}

# installing pgFouine
for i in include version.php; do
	cp -rp $i %{buildroot}/%{_datadir}/%{name}/
done

install -m 755 pgfouine.php %{buildroot}/%{_bindir}/
install -m 755 pgfouine_vacuum.php %{buildroot}/%{_bindir}/

%clean
rm -rf %{buildroot}

%files
%defattr(-, root, root)
%doc AUTHORS ChangeLog COPYING THANKS README RELEASE pgfouine-tutorial.txt
%attr(0755, root, root) %{_bindir}/pgfouine.php
%attr(0755, root, root) %{_bindir}/pgfouine_vacuum.php
%{_datadir}/%{name}

%changelog
* Wed Feb 24 2010  Guillaume Smet <guillaume.smet@gmail.com> 1.2-1
- Update to 1.2

* Sun Apr 26 2009  Guillaume Smet <guillaume-pg@smet.org> - 1.1-1
- Update to 1.1

* Thu Aug 28 2008 Tom "spot" Callaway <tcallawa@redhat.com> - 1.0-3
- fix license tag

* Sun Jun 3 2007 Devrim Gunduz <devrim@CommandPrompt.com> - 1.0-2
- Bumped up spec version

* Sun Apr 1 2007 Devrim Gunduz <devrim@CommandPrompt.com> - 1.0-1
- Update to 1.0

* Tue Dec 12 2006 Devrim Gunduz <devrim@CommandPrompt.com> - 0.7.2-1
- Update to 0.7.2

* Thu Nov 30 2006 Devrim Gunduz <devrim@CommandPrompt.com> - 0.7.1-2
- Added tutorial.txt per bugzilla review

* Sat Oct 28 2006 Guillaume Smet <guillaume-pg@smet.org> - 0.7.1-1
- released 0.7.1

* Sun Sep 3 2006 Guillaume Smet <guillaume-pg@smet.org> - 0.7-4
- fixed spec according to bugzilla #202901 comment #2

* Thu Aug 18 2006 Devrim Gunduz <devrim@CommandPrompt.com> - 0.7-3
- fixed spec, per bugzilla review

* Thu Aug 17 2006 Devrim Gunduz <devrim@CommandPrompt.com> - 0.7-2
- fixed rpmlint warnings, and made cosmetic changes

* Thu Aug 17 2006 Guillaume Smet <guillaume-pg@smet.org>
- released 0.7

* Thu Aug 10 2006 Guillaume Smet <guillaume-pg@smet.org>
- fixed RPM packaging for 0.7

* Wed Jul 19 2006 Guillaume Smet <guillaume-pg@smet.org>
- added pgfouine_vacuum.php

* Sun May 21 2006 Guillaume Smet <guillaume-pg@smet.org>
- released 0.6

* Sun Mar 26 2006 Guillaume Smet <guillaume-pg@smet.org>
- released 0.5

* Tue Jan 10 2006 Guillaume Smet <guillaume-pg@smet.org>
- released 0.2.1

* Sun Dec 4 2005 Guillaume Smet <guillaume-pg@smet.org>
- released 0.2

* Fri Nov 18 2005 Guillaume Smet <guillaume-pg@smet.org>
- initial RPM packaging
