import Tool from './pages/Tool'
import EditOverview from './pages/EditOverview'
import Attachment from './pages/Attachment'
import Create from './pages/Create'
import BulkImport from './pages/BulkImport'
import ActivityLog from './pages/ActivityLog'

Nova.booting((app, store) => {
  Nova.inertia('ComplianceOverview', Tool)
  Nova.inertia('EditOverview', EditOverview)
  Nova.inertia('Attachment', Attachment)
  Nova.inertia('Create', Create)
  Nova.inertia('ActivityLog', ActivityLog)
  Nova.inertia('BulkImport', BulkImport)
})