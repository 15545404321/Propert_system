Vue.component('Add', {
	template: `
		<el-drawer title="追加应收"  direction="rtl" size="1200px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="资产全称" prop="fcxx_id">
							<el-select  remote :remote-method="remoteFcxxidList"  style="width:100%" v-model="form.fcxx_id" filterable clearable placeholder="请选择资产全称">
								<el-option v-for="(item,i) in fcxx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
							<!--<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.fcxx_id" :options="fcxx_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择资产全称"/>-->
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用名称" prop="fydy_id">
							<el-select @change="selectFybz_id"  style="width:100%" v-model="form.fydy_id" filterable clearable placeholder="请选择费用名称">
								<el-option v-for="(item,i) in fydy_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="fybz_id_show">
					<el-col :span="24">
						<el-form-item label="计费标准" prop="fybz_id">
							<el-select   style="width:100%" v-model="form.fybz_id" filterable clearable placeholder="请选择计费标准">
								<el-option v-for="(item,i) in fybz_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="单次应收" prop="zjys_dcys">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.zjys_dcys" clearable :min="0" placeholder="请输入单次应收"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="使用数量" prop="zjys_sysl">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.zjys_sysl" clearable :min="0" placeholder="请输入使用数量"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="本次应收" prop="zjys_bcys">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.zjys_bcys" clearable :min="0" placeholder="请输入本次应收"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开始日期" prop="zjys_ktime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.zjys_ktime" clearable placeholder="请输入开始日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="结束时间" prop="zjys_jtime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.zjys_jtime" clearable placeholder="请输入结束时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="追加摘要" prop="zjys_zjzy">
							<el-input  v-model="form.zjys_zjzy" autoComplete="off" clearable  placeholder="请输入追加摘要"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" style="text-align:center;margin:0 0 30px 0">
				<el-button :size="size" style="width:35%;" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" style="width:35%;" @click="closeForm">取 消</el-button>
			</div>
			</div>
		</el-drawer>
	`
	,
	components:{
		'treeselect':VueTreeselect.Treeselect,
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
				fcxx_id:'',
				fydy_id:'',
				fybz_id:'',
				zjys_ktime:curentTime(),
				zjys_jtime:curentTime(),
				zjys_zjzy:'',
				shop_id:'',
				xqgl_id:'',
				member_id:'',
			},
			fcxx_ids:[],
			fydy_ids:[],
			fybz_ids:[],
			loading:false,
			rules: {
				fcxx_id:[
					{required: true, message: '资产全称不能为空', trigger: 'change'},
				],
				fydy_id:[
					{required: true, message: '费用名称不能为空', trigger: 'change'},
				],
				zjys_bcys:[
					{required: true, message: '本次应收不能为空', trigger: 'blur'},
					{pattern:/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/, message: '本次应收格式错误'}
				],
			},
			fybz_id_show:false
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Zjys/getFieldList').then(res => {
					if(res.data.status == 200){
						this.fydy_ids = res.data.data.fydy_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form.member_id = this.info.member_id
		},
		selectFybz_id(val){
			this.form.fybz_id = ''
			axios.post(base_url + '/Zjys/getFybz_id',{fydy_id:val}).then(res => {
				if(res.data.status == 200){
					console.log('selectFybz_id',res)
					this.fybz_ids = res.data.data
					this.fybz_id_show = res.data.fylx_id != 3
				}
			})
		},
		remoteFcxxidList(val){
			val += ','+this.info.member_id
			axios.post(base_url + '/Zjys/remoteFcxxidList',{queryString:val}).then(res => {
				if(res.data.status == 200){
					this.fcxx_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Zjys/add',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		normalizer(node) {
			if (node.children && !node.children.length) {
				delete node.children
			}
			return {
				id: node.val,
				label: node.key,
				children: node.children
			}
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			this.fcxx_ids = []
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
