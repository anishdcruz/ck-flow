import Row from '@js/components/grid/Row.vue'
import Col from '@js/components/grid/Col.vue'
import Panel from '@js/components/panel/Panel.vue'
import Button from '@js/components/button/Button.vue'
import ButtonGroup from '@js/components/button/ButtonGroup.vue'
import Icon from '@js/components/icon/Icon.vue'

import Sidebar from '@js/components/sidebar/Sidebar.vue'
import SidebarItem from '@js/components/sidebar/SidebarItem.vue'
import SidebarGroup from '@js/components/sidebar/SidebarGroup.vue'

import Loading from '@js/components/helpers/Loading.vue'
import Width from '@js/components/helpers/Width.vue'

import Tag from '@js/components/tag/Tag.vue'
import Status from '@js/components/tag/Status.vue'
import FieldTab from '@js/components/tabs/FieldTab.vue'

import Input from '@js/components/form/Input.vue'
import Textarea from '@js/components/form/Textarea.vue'
import FormGroup from '@js/components/form/FormGroup.vue'

import TagInput from '@js/components/form/TagInput.vue'
import TypeaheadTable from '@js/components/form/TypeaheadTable.vue'

import Select from '@js/components/form/Select.vue'
import SimpleSelect from '@js/components/form/SimpleSelect.vue'
import Switch from '@js/components/form/Switch.vue'

import Alert from '@js/components/alert/Alert.vue'

import Modal from '@js/components/modal/Modal.vue'
import Dropdown from '@js/components/dropdown/Dropdown.vue'
import DropdownMenu from '@js/components/dropdown/DropdownMenu.vue'
import DropdownItem from '@js/components/dropdown/DropdownItem.vue'
import DropdownLink from '@js/components/dropdown/DropdownLink.vue'

import LoadingBar from '@js/components/loading-bar'
import Message from '@js/components/message'

import Typeahead from '@js/components/form/Typeahead.vue'
import TypeaheadArray from '@js/components/typeahead/TypeaheadArray.vue'
import TypeaheadUrl from '@js/components/typeahead/TypeaheadUrl.vue'

import Image from '@js/components/upload/Image.vue'

import Table from '@js/components/table/Table.vue'
import TableRow from '@js/components/table/TableRow.vue'
import TableHead from '@js/components/table/TableHead.vue'
import TableBody from '@js/components/table/TableBody.vue'
import TableCell from '@js/components/table/TableCell.vue'
import TableFooter from '@js/components/table/TableFooter.vue'

import SimpleTab from '@js/components/tabs/SimpleTab.vue'
import Filterable from '@js/components/filterable/Filterable.vue'
import Group from '@js/components/helpers/Group.vue'
import Mini from '@js/components/filterable/Mini.vue'

import Http from '@js/lib/Http'

// partials

import Form from '@js/partials/Form.vue'

const components = {
	Row,
	Col,
	Panel,
	Button,
	ButtonGroup,
	Icon,
	Sidebar,
	SidebarItem,
	SidebarGroup,
	Loading,
	Width,
	Tag,
	Input,
	Textarea,
	FormGroup,
	TagInput,
	Select,
	SimpleSelect,
	Switch,
	Alert,
	Modal,
	Dropdown,
	DropdownMenu,
	DropdownItem,
	DropdownLink,
	Typeahead,
	TypeaheadArray,
	TypeaheadUrl,
	Image,
	Table,
	TableCell,
	TableHead,
	TableRow,
	TableBody,
	TableFooter,
	SimpleTab,
	Filterable,
	FieldTab,
	Form,
	Status,
	Group,
	TypeaheadTable,
	Mini
}

const actions = {
	LoadingBar,
	Message,
	Http
}

function install(Vue) {
	if(install.installed) return

	for(const item in components) {
		if(components[item].name) {
			Vue.component(components[item].name, components[item])
		}
	}

	Vue.prototype.$bar = LoadingBar
	Vue.prototype.$message = Message
	Vue.prototype.$http = Http
}

if(typeof window !== 'undefined' && window.Vue) {
	install(window.Vue)
}

export default {
	install,
	...components,
	...actions
}