const permissionsInfo = [
  {
    'name': 'Zarządzanie uprawnieniami',
    'value': 'managePermissions',
    'section': 'Użytkownicy',
  },
  {
    'name': 'Zarządzanie dniami wolnymi',
    'value': 'manageHolidays',
    'section': 'Urlopy',
  },
  {
    'name': 'Zarządzanie użytkownikami',
    'value': 'manageUsers',
    'section': 'Użytkownicy',
  },
  {
    'name': 'Zarządzanie kluczami',
    'value': 'manageKeys',
    'section': 'Biuro',
  },
  {
    'name': 'Zarządzanie sprzętem',
    'value': 'manageEquipment',
    'section': 'Biuro',
  },
  {
    'name': 'Zarządzanie technologiami',
    'value': 'manageTechnologies',
    'section': 'Zespół',
  },
  {
    'name': 'Zarządzanie CV',
    'value': 'manageResumes',
    'section': 'Zespół',
  },
  {
    'name': 'Zarządzanie benefitami',
    'value': 'manageBenefits',
    'section': 'Zespół',
  },
  {
    'name': 'Zarządzanie limitami urlopowymi',
    'value': 'manageVacationLimits',
    'section': 'Urlopy',
  },
  {
    'name': 'Zarządzanie wnioskami jako przełożony administracyjny',
    'value': 'manageRequestsAsAdministrativeApprover',
    'section': 'Urlopy',
  },
  {
    'name': 'Zarządzanie wnioskami jako przełożony techniczny',
    'value': 'manageRequestsAsTechnicalApprover',
    'section': 'Urlopy',
  },
  {
    'name': 'Tworzenie wniosków w imieniu pracownika',
    'value': 'createRequestsOnBehalfOfEmployee',
    'section': 'Urlopy',
  },
  {
    'name': 'Przeglądanie wykorzystania urlopu',
    'value': 'listMonthlyUsage',
    'section': 'Urlopy',
  },
  {
    'name': 'Przeglądanie wszystkich wniosków',
    'value': 'listAllRequests',
    'section': 'Urlopy',
  },
  {
    'name': 'Natychmiastowe zatwierdzanie wniosków',
    'value': 'skipRequestFlow',
    'section': 'Urlopy',
  },
  {
    'name': 'Nadchodzące i zaległe badania medycyny pracy',
    'value': 'receiveUpcomingAndOverdueMedicalExamsNotification',
    'section': 'Powiadomienia',
  },
  {
    'name': 'Nadchodzące i zaległe szkolenia BHP',
    'value': 'receiveUpcomingAndOverdueOhsTrainingNotification',
    'section': 'Powiadomienia',
  },
  {
    'name': 'Tworzenie raportu benefitów',
    'value': 'receiveBenefitsReportCreationNotification',
    'section': 'Powiadomienia',
  },
  {
    'name': 'Podsumowania wniosków urlopowych',
    'value': 'receiveVacationRequestsSummaryNotification',
    'section': 'Powiadomienia',
  },
  {
    'name': 'Nowy wniosek oczekuje na potwierdzenie',
    'value': 'receiveVacationRequestWaitsForApprovalNotification',
    'section': 'Powiadomienia',
  },
  {
    'name': 'Status wniosku urlopowego został zmieniony',
    'value': 'receiveVacationRequestStatusChangedNotification',
    'section': 'Powiadomienia',
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
