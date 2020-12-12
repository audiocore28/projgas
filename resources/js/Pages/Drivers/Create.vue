<template>
	<div>
		<Dashboard>
			<jet-section-title>
				<template v-slot:title>Add Driver</template>
				<template v-slot:description>Driver's Personal Information</template>
			</jet-section-title>
			<hr class="py-2"/>

			<jet-label>ID</jet-label>
			<jet-input value="12-345-678"/>
			<jet-label>First Name</jet-label>
         <jet-input value="Peter" />
			<jet-label>Last Name</jet-label>
         <jet-input value="Parker" />
			<jet-label>Address</jet-label>
         <jet-input value="San Jose" />
			<jet-label>License No.</jet-label>
         <jet-input value="12-345-678" />
			<jet-label>Date of Birth</jet-label>
         <jet-input value="01/02/10" />
			<jet-label>Age</jet-label>
         <jet-input value="23" />
			<jet-label>Gender</jet-label>
         <jet-input value="Male" />
			<jet-label>Date Hired</jet-label>
         <jet-input value="01/02/15" />
			<jet-label>Status</jet-label>
         <jet-input value="Single" />
			<jet-label>Contact No.</jet-label>
         <jet-input value="8989-891123" />
         <jet-button>Save</jet-button>
		</Dashboard>
	</div>
</template>

<script>
import Dashboard from '@/Shared/Dashboard'
import JetInput from '@/Jetstream/Input'
import JetLabel from '@/Jetstream/Label'
import JetButton from '@/Jetstream/Button'
import JetSectionTitle from '@/Jetstream/SectionTitle'

export default {
	props: ['foo'],
	components: 	{
		Dashboard,
		JetInput,
		JetLabel,
		JetButton,
		JetSectionTitle,
	},
}
</script>
