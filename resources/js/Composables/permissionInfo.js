const permissionsInfo = [
  {
    'name': 'Zarządzanie uprawnieniami',
    'value': 'manage permissions',
    'section': 'Użytkownicy',
  },
  {
    'name': 'Zarządzanie dniami wolnymi',
    'value': 'manage holidays',
    'section': 'Urlopy',
  },
  {
    'name': 'Zarządzanie użytkownikami',
    'value': 'manage users',
    'section': 'Użytkownicy',
  },
  {
    'name': 'Zarządzanie kluczami',
    'value': 'manage keys',
    'section': 'Biuro',
  },
  {
    'name': 'Zarządzanie technologiami',
    'value': 'manage technologies',
    'section': 'Zespół',
  },
  {
    'name': 'Zarządzanie CV',
    'value': 'manage resumes',
    'section': 'Zespół',
  },
  {
    'name': 'Zarządzanie benefitami',
    'value': 'manage benefits',
    'section': 'Zespół',
  },
  {
    'name': 'Zarządzanie limitami urlopowymi',
    'value': 'manage vacation limits',
    'section': 'Urlopy',
  },
  {
    'name': 'Zarządzanie wnioskami jako przełożony administracyjny',
    'value': 'manage requests as administrative approver',
    'section': 'Urlopy',
  },
  {
    'name': 'Zarządzanie wnioskami jako przełożony techniczny',
    'value': 'manage requests as technical approver',
    'section': 'Urlopy',
  },
  {
    'name': 'Tworzenie wniosków w imieniu pracownika',
    'value': 'create requests on behalf of employee',
    'section': 'Urlopy',
  },
  {
    'name': 'Przeglądanie wykorzystania urlopu',
    'value': 'list monthly usage',
    'section': 'Urlopy',
  },
  {
    'name': 'Przeglądanie wszystkich wniosków',
    'value': 'list all requests',
    'section': 'Urlopy',
  },
  {
    'name': 'Natychmiastowe zatwierdzanie wniosków',
    'value': 'skip request flow',
    'section': 'Urlopy',
  },
]

export function usePermissionInfo() {
  const getPermissions = () => permissionsInfo

  const findPermission = value => permissionsInfo.find(permission => permission.value === value)
  
  const findGroupedPermissions = (permissions) => {
    const groupedPermissions= new Map()

    getSections().forEach(section => {
      groupedPermissions.set(section, [])
    })

    groupedPermissions.set('Inne', [])

    Object.keys(permissions).forEach(permissionKey => {
      const permissionInfo = findPermission(permissionKey) || { section: 'Inne', name: permissionKey, value: permissionKey }

      groupedPermissions.get(permissionInfo.section).push(permissionInfo)
    })

    return groupedPermissions
  }

  const getSections = () => {
    const sections = []
    permissionsInfo.forEach(permission => {
      if (!sections.includes(permission.section)) {
        sections.push(permission.section)
      }
    })

    return sections
  }

  return {
    getPermissions,
    findGroupedPermissions,
    findPermission,
    getSections,
  }
}
