Vue.component('Add', {
	template: `
		<el-dialog title="添加" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="资产类型" prop="zclx_id">
							<el-select   style="width:100%" v-model="form.zclx_id" filterable clearable placeholder="请选择资产类型">
								<el-option v-for="(item,i) in zclx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.zclx_id==1">
					<el-col :span="24">
						<el-form-item label="楼宇/单元" prop="louyu_id">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.louyu_id" :options="louyu_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择楼宇/单元"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.zclx_id==1">
					<el-col :span="24">
						<el-form-item label="房产名称" prop="fcxx_id">
							<el-select   style="width:100%" v-model="form.fcxx_id" filterable clearable placeholder="请选择房产名称">
								<el-option v-for="(item,i) in fcxx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.zclx_id==2">
					<el-col :span="24">
						<el-form-item label="停车场地" prop="tccd_id">
							<el-select @change="selectCewei_id"  style="width:100%" v-model="form.tccd_id" filterable clearable placeholder="请选择停车场地">
								<el-option v-for="(item,i) in tccd_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.zclx_id==2">
					<el-col :span="24">
						<el-form-item label="车位资产" prop="cewei_id">
							<el-select   style="width:100%" v-model="form.cewei_id" filterable clearable placeholder="请选择车位资产">
								<el-option v-for="(item,i) in cewei_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开始日期" prop="start_time">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.start_time" clearable placeholder="请输入开始日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="结束日期" prop="end_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.end_time" clearable placeholder="请输入结束日期"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.zclx_id == 1">
					<el-col :span="24">
						<el-form-item label="住户类型" prop="khlx_id">
							<el-select   style="width:100%" v-model="form.khlx_id" filterable clearable placeholder="请选择住户类型">
								<el-option v-for="(item,i) in khlx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row v-if="form.zclx_id == 1">
					<el-col :span="24">
						<el-form-item label="住户属性" prop="glzcgl_type">
							<el-checkbox-group v-model="form.glzcgl_type">
								<el-checkbox key="0" label="1">主住户</el-checkbox>
							</el-checkbox-group>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
		</el-dialog>
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
	},
	data(){
		return {
			form: {
				shop_id:'',
				xqgl_id:'',
				member_id:'',
				zclx_id:'',
				fcxx_id:'',
				tccd_id:'',
				cewei_id:'',
				start_time:'',
				end_time:'',
				khlx_id:'',
				glzcgl_type:[],
				shop_admin_id:'',
				glzcgl_time:'',
				member_id:'',
			},
			zclx_ids:[],
			louyu_ids:[],
			fcxx_ids:[],
			tccd_ids:[],
			cewei_ids:[],
			khlx_ids:[],
			loading:false,
			rules: {
				zclx_id:[
					{required: true, message: '资产类型不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Glzcgl/getFieldList').then(res => {
					if(res.data.status == 200){
						this.zclx_ids = res.data.data.zclx_ids
						this.louyu_ids = res.data.data.louyu_ids
						this.tccd_ids = res.data.data.tccd_ids
						this.khlx_ids = res.data.data.khlx_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			const myURL = new URL(window.location.href)
			const urlobj = param2Obj(myURL.href)
			this.form.member_id = urlobj.member_id
		},
		selectFcxx_id(val){
			this.form.fcxx_id = ''
			axios.post(base_url + '/Glzcgl/getFcxx_id',{louyu_id:val}).then(res => {
				if(res.data.status == 200){
					this.fcxx_ids = res.data.data
				}
			})
		},
		selectCewei_id(val){
			this.form.cewei_id = ''
			axios.post(base_url + '/Glzcgl/getCewei_id',{tccd_id:val}).then(res => {
				if(res.data.status == 200){
					this.cewei_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Glzcgl/add',this.form).then(res => {
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
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
